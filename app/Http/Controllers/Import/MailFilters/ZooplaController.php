<?php

namespace App\Http\Controllers\Import\MailFilters;


include('C:\xampp\htdocs\MLM\app\Http\Controllers\Import\IncomingController.php');
use App\Http\Controllers\Import\IncomingController;


include('C:\xampp\htdocs\MLM\app\Http\Controllers\HTMLController.php');
use App\Http\Controllers\HTMLController;


//use App\Http\Controllers\Import\MailFilters\Zoopla\EnquiryController; TOCHECK


class ZooplaController extends IncomingController
{
    private $sourced_from = "members@zpg.co.uk";
    private $source_folder = "INBOX";
    private $destination_folder = "INBOX/Zoopla/test";
    protected $source = "Portal";
    protected $sub_source = "Zoopla";

    public function __construct()
    {
        parent::__construct();
    }

    public function getLeadFromMailItem($mailItem)
    {
        return [
            "name" => trim($mailItem->name()),
            "email" => trim($mailItem->email()),
            "telephone" => trim($mailItem->telephone()),
            "country" => $mailItem->country(),
            "reason_for_buying" => $mailItem->reasonForBuying(),
            "comments" => trim($mailItem->comments()),
            "language_used" => $this->detectLanguage($mailItem->comments()),
            "property_reference_number" => trim($mailItem->propertyReference()),
            "property_link" => trim($mailItem->propertyLink()),
            "source" => $this->source,
            "sub_source" => $this->sub_source,
        ];
    }

    public function mailItem($html)
    {
        try
        {
            //return new EnquiryController($html->domObject()); TOCHECK
        }
        catch (Exception $e)
        {
            return false;
        }
    }

    public function processMailItem($inboxMailItem)
    {
        if($inboxMailItem->sender[0]->mail == $this->sourced_from)
        {
            $html = new HTMLController($inboxMailItem->bodies["html"]->content);
            $mailItem = $this->mailItem($html);

            if($mailItem)
            {
                $lead = $this->getLeadFromMailItem($mailItem);

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
            if(!$this->createFolder($this->destination_folder))
            {
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
            catch(Exception $e)
            {
                echo $inboxMailItem->bodies["html"]->content;
                $html = new HTMLController($inboxMailItem->bodies["html"]->content);
                dd($e);
                dd($html->domObject());
            }

            if($count > $batch_size)
            {
                dd("Count exceeds batch size - Zoopla");
                break;
            }
        }
    }
}
