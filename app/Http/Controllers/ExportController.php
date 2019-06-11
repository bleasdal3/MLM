<?php

namespace App\Http\Controllers;

use App\Lead;
use GuzzleHttp\Client;
use \App\Http\Controllers\SourceController;
class ExportController extends Controller
{
    private $exported_count;

    public function __construct()
    {
        $this->exported_count = 0;
    }

    /**
     * @param $lead
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function submitToSalesforceWebToLead($lead){
        $client = new Client();
        $salesforceURL = 'https://webto.salesforce.com/servlet/servlet.WebToLead?encoding=UTF-8';
        $sourceController = new SourceDataController;
        $source_data = $sourceController->getCode($lead);
        $motivationController = new MotivationController;
        $motivation = $motivationController->rightmoveReasonForBuying($lead->reason_for_buying)->getMotivation();
        $nameParser = new NameParser($lead->name);
        $data = [
            'oid' => '00Db0000000YtkL',
            'retURL' => url('/thank-you'),
            'first_name' => $nameParser->firstName(),
            'last_name' => $nameParser->lastName(),
            'phone' => $lead->telephone,
            'email' => $lead->email,
            'lead_source' => $lead->source, // Source
            '00Nb0000009UpEQ' => $lead->source, // Source
            '00Nb0000003z8N8' => $lead->sub_source, //Sub Source
            '00Nb0000009nQZD' => 1, //Overseas lead
            '00Nb0000003zAb0' => $lead->property_link, //Referrer from
            '00Nb0000003z8RL' => $lead->keyword, //Keywords
            '00Nb000000AXs4j' => $lead->property_link, //Page currently on
            '00Nb0000009mvsY' => $source_data,
            '00Nb000000AYTYM' => $motivation, //Motivation
            '00Nb00000041GOC' => $lead->comments,
            '00Nb0000009nfCL' => $lead->property_link,
            '00Nb000000AXJ26' => $lead->property_reference_number,
            '00Nb000000AXGra' => 'Initial Lead Queue',
            '00N0N00000AbVup' => $lead->country,
            '00N0N00000AbVuk' => "MLM",
        ];
        return $client->request('POST', $salesforceURL, ['form_params' => $data]);
    }

    public function pendingToWebToLead(){
	    $leads = Lead::where("exported", "=" , false)->get();
	    foreach ($leads as $lead){
	        if($this->submitToSalesforceWebToLead($lead)){
	            $lead->exported = true;
	            $lead->save();
	            $this->exported_count++;
            }else{
	            dd($lead);
            }
        }
    }

    public function count(){
        return $this->exported_count;
    }
    
}
