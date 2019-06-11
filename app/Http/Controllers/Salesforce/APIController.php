<?php

namespace App\Http\Controllers\Salesforce;

use App\Http\Controllers\Controller;
use \stdClass;
use GuzzleHttp\Client;

class APIController extends Controller
{
	public $instance = "";
	public $APIVersion = "v39.0";

	public function __construct()
    {
    	$this->middleware('auth');
    	$this->instance = env("SALESFORCE_INSTANCE_URL");
    	$this->login();
    }

	public function login(){
		$url = $this->instance."/services/oauth2/token";
		$fields = array(
			'client_id' => env("SALESFORCE_CLIENT_ID"),
			'client_secret' => env("SALESFORCE_CLIENT_SECRET"),
			'username' => env("SALESFORCE_USERNAME"),
			'password' => env("SALESFORCE_PASSWORD").env("SALESFORCE_ACCESS_TOKEN"),
			'grant_type' => "password",
		);
		$client = new Client();
		$result = $client->post($url, [
		    'form_params' => $fields
		]);
	    session(['salesforce_access_token' => json_decode($result->getBody()->getContents())]);
	}

	public function create(){
		return view("salesforce.login");
	}

	public function postRecord($object, $data){
		$client = new Client();
		$result = $client->post($this->instance.'/services/data/'.$this->APIVersion."/sobjects/".$object, [
		    'json' => $data,
		    'headers' => [
		    	'Authorization' => 'Bearer ' . session('salesforce_access_token')->access_token,
		    	'Content-Type' => "application/json; charset=UTF-8"
	    	]	
		]);
	    return $result;
	}

	public function searchForRecord($object, $field, $value){
		$client = new Client();
		$result = $client->get($this->instance.'/services/data/'.$this->APIVersion."/sobjects/".$object."/".$field."/".$value, [
		    'headers' => [
		    	'Authorization' => 'Bearer ' . session('salesforce_access_token')->access_token,
		    	'Content-Type' => "application/json; charset=UTF-8"
	    	]
		]);
	    return $result;
	}

	public function request($url, $data = false)
	{
		$url = $this->instance . $url;
		$client = new Client();
		if(session('salesforce_access_token') === null){
			$this->login();
		}
		$result = $client->get($url, [
		    'body' => $data,
		    'headers' => ['Authorization' => 'Bearer ' . session('salesforce_access_token')->access_token]
		]);
		return $result->getBody()->getContents();
	}

	public function returnJSON($url){
       	return $this->request($url);
	}

	public function returnObject($url){
		$json = $this->returnJSON($url);
		return json_decode($json);
	}

	public function reformatReportData($data){
		$columns = [];
    	foreach($data->reportExtendedMetadata->detailColumnInfo as $key=>$value){
    		$columns[] = explode(".", $key)[1];
    	}
    	foreach($data->factMap as $key=>$value){
			$rows = $value->rows;
    	}
    	$reformattedData = [];
    	foreach ($rows as $row) {
    		$obj = new stdClass;
    		$i = 0;
    		foreach($columns as $column){
    			$obj->$column = $row->dataCells[$i]->value;
    			$i++;
    		}
    		$reformattedData[] = $obj;
    	}
    	return $reformattedData;
	}

	public function query($query){
		$url = "/services/data/".$this->APIVersion."/query?q=".$query;
		return $this->returnObject($url);
	}

    public function allObjects(){
        $url = "/services/data/".$this->APIVersion."/sobjects";
        $data = $this->returnObject($url);
        $objects = $data->sobjects;
        return view("salesforce.all-objects")->with("objects", $objects);
    }

    public function allLeads(){
    	$data = $this->query("SELECT+id,firstname,lastname,rating,lead_rating__c,CreatedDate+FROM+Lead+WHERE+lead_rating__c!=null");
    	dd($data);
    }

    public function report($report_id){
    	$url = "/services/data/".$this->APIVersion."/analytics/reports/".$report_id;
		return $this->returnObject($url);
    }

    public function allLeadsReport(){
    	$data = $this->report("00Ob0000004VaAs");
    	return $this->reformatReportData($data);
    }


}
