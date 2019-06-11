<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;

class SourceDataController extends Controller
{
    private $region_query_url = "https://pfm.sequreinternational.com/api/property/region/";

    private $lead;

    public function regionConvertToQueue($region){
        switch ($region){
            case "Costa del Sol":
                return "CostaDelSol";
                break;
            case "Costa Blanca North":
                return "CostaBlancaNorth";
                break;
            case "Costa Blanca South":
                return "CostaBlancaSouth";
                break;
            default:
                return "GeneralEnquiry";
        }
    }

    private function language(){
        if($this->lead->language_used == "fr" || $this->lead->language_used == "es"){
            return "_".strtoupper($this->lead->language_used);
        }
        return "";
    }

    private function leadQueue(){
        $client = new Client([
            'verify' => false
        ]);
        $property_reference = $this->lead->property_reference_number;
        if($property_reference != ""){
            $data = json_decode($client->request('GET', $this->region_query_url.$property_reference)->getBody()->getContents());
            if(isset($data->result)){
                $region = $data->result;
                return "_".$this->regionConvertToQueue($region);
            }
        }
        return "_GeneralEnquiry";
    }

    private function code(){
        return "Ovs".$this->language().$this->leadQueue();
    }

    public function getCode($lead){
        $this->lead = $lead;
        return $this->code();
    }

}
