<?php

namespace App\Http\Controllers\Import\MailFilters;

include('C:\xampp\htdocs\MLM\app\Http\Controllers\HTMLController.php');
include('C:\xampp\htdocs\MLM\app\Http\Controllers\Import\IncomingController.php');
include('C:\xampp\htdocs\MLM\app\Http\Controllers\RightmoveEmailParser');

use App\Http\Controllers\Import\IncomingController;
use App\Http\Controllers\HTMLController;
use App\Http\Controllers\RightmoveEmailParser;

class RightmoveController extends IncomingController
{
	private $sourced_from = "autoresponder@rightmove.co.uk";

	private $source_folder = "INBOX";
	private $destination_folder = "INBOX/Done/Rightmove/systematicallytransacted";

	protected $source = "Portal";
    protected $sub_source = "Rightmove";

	public function __construct()
    {
        parent::__construct();
    }

    public function get_property_reference_from_rightmove_long_reference($rightmove_long_reference){
		$rightmove_reference_array = explode("_", $rightmove_long_reference);
		if(count($rightmove_reference_array) == 4){
			return $rightmove_reference_array[count($rightmove_reference_array)-2]."/".$rightmove_reference_array[count($rightmove_reference_array)-1];
		}elseif(count($rightmove_reference_array) == 3){
			return $rightmove_reference_array[count($rightmove_reference_array)-1];
		}
		return $rightmove_long_reference;
	}

	public function getLeadFromMailItem(RightmoveEmailParser $mailItem)
	{
		return [
            "name" => $mailItem->name(),
            "email" => $mailItem->email(),
            "telephone" => $mailItem->telephone(),
            "country" => $mailItem->country(),
            "reason_for_buying" => $mailItem->reasonForBuying(),
            "comments" => $mailItem->comments(),
            "language_used" => $this->detectLanguage($mailItem->comments()),
            "property_reference_number" => $this->get_property_reference_from_rightmove_long_reference($mailItem->reference()),
            "property_link" => $mailItem->propertyLink(),
            "source" => $this->source,
            "sub_source" => $this->sub_source,
        ];
	}

	public function processMailItem($inboxMailItem)
	{
		if($inboxMailItem->sender[0]->mail == $this->sourced_from)
		{
			$mailItemParsed = new RightmoveEmailParser($inboxMailItem->bodies["html"]->content);
			if($mailItemParsed)
			{
				$lead = $this->getLeadFromMailItem($mailItemParsed);
				if($this->createLead($lead))
				{
					$this->moveToFolder($inboxMailItem, $this->destination_folder);
				}
			}
		}
	}

	public function processMailItems()
	{
		$inboxMailItems = $this->getFolderMailItems($this->source_folder);
		$readMailbox = $this->getFolderObj($this->destination_folder);
		if(!$readMailbox)
		{
			if(!$this->createFolder($this->destination_folder)){
				dd("Failed to create folder " . $this->destination_folder);
			}
		}

		$batch_size = 300;
		$count = 0;

		foreach($inboxMailItems as $inboxMailItem)
		{
			try
			{
				$this->processMailItem($inboxMailItem);
				$count++;
			}
			catch(\Exception $e)
			{
				echo $inboxMailItem->bodies["html"]->content;
				$html = new HTMLController($inboxMailItem->bodies["html"]->content);
				dd($e);
				dd($html->domObject());
			}
			
			if($count > $batch_size)
			{
				dd('Count exceeds batch size - Rightmove');
				break;
			}
		}
	}


}
