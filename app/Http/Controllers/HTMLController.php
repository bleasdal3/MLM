<?php

namespace App\Http\Controllers;

use \DOMDocument;

class HTMLController extends Controller
{

	private $dom;

	public function __construct($html){
		$this->dom = $this->convert_to_object($html);
	}
    
    public function convert_to_object($html) {
	    $dom = new DOMDocument();
	    libxml_use_internal_errors(true);
	    $dom->loadHTML($html);
	    libxml_use_internal_errors(false);
	    return $this->element_to_obj($dom->documentElement);
	}

	public function element_to_obj($element) {
		if(isset($element->tagName)){
		    $obj = (object)array( "tag" => $element->tagName );
		    foreach ($element->attributes as $attribute) {
		    	$attributeName = $attribute->name;
		        $obj->$attributeName = $attribute->value;
		    }
		    foreach ($element->childNodes as $subElement) {
		        if ($subElement->nodeType == XML_TEXT_NODE) {
		            $obj->html = $subElement->wholeText;
		        }
		        else {
		            $obj->children[] = $this->element_to_obj($subElement);
		        }
		    }
		    return $obj;
	    }else{
	    	return null;
	    }
	}

	public function domObject(){
		return $this->dom;
	}

	public function body(){
		if($this->dom->children[1]->tag = "Body"){
			return $this->dom->children[1];
		}
		return null;
	}

	public function head(){
		if($this->dom->children[0]->tag = "Head"){
			return $this->dom->children[0];
		}
		return null;
	}

}
