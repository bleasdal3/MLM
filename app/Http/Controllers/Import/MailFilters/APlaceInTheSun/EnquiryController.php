<?php

namespace App\Http\Controllers\Import\MailFilters\APlaceInTheSun;

use App\Http\Interfaces\MailItem;

class EnquiryController implements MailItem
{
    private $dom;

    public function __construct($domObject){
        $this->dom = $domObject;
    }

    public function rootTable(){
        return $this->dom->children[1]->children[0]->children[0]->children[0]->children[0]->children;
    }

    public function customerTable(){
        return $this->rootTable()[1]->children[0]->children[0];
    }

    public function propertyTable(){
        return $this->rootTable()[3]->children[0]->children[0]->children[0]->children[0]->children[0];
    }

    /**
     * Get the name from the email body
     *
     * @return string
     */
    public function name(){
        return $this->customerTable()->children[0]->children[1]->html;
    }

    /**
     * Get the email address from the email body
     *
     * @return string
     */
    public function email(){
        return $this->customerTable()->children[1]->children[1]->children[0]->html;
    }

    /**
     * Get the phone from the email body
     *
     * @return string
     */
    public function telephone(){
        if(isset($this->customerTable()->children[2]->children[1]->html)){
            return $this->customerTable()->children[2]->children[1]->html;
        }else{
            return "";
        }
    }

    /**
     * Get the property reference from the email body
     *
     * @return string
     */
    public function propertyReference(){
        return $this->propertyTable()->children[1]->children[1]->html;
    }

    /**
     * Get the country from the email body
     *
     * @return string
     */
    public function country(){
        return "";
    }

    /**
     * Get the reason for buying from the email body
     *
     * @return string
     */
    public function reasonForBuying(){
        return "";
    }

    /**
     * Get the comments from the email body
     *
     * @return string
     */
    public function comments(){
        if(isset($this->customerTable()->children[3]->children[1]->html)){
            return $this->customerTable()->children[3]->children[1]->html;
        }else{
            return "";
        }

    }

    /**
     * Get the property reference from the email body
     *
     * @return string
     */
    public function propertyLink(){
        return $this->propertyTable()->children[3]->children[1]->children[0]->href;
    }
}
