<?php

namespace App\Http\Controllers\Import;

use App\Http\Controllers\Controller;

include('C:\xampp\htdocs\MLM\vendor\webklex\laravel-imap\src\IMAP\Facades\Client.php');
use Webklex\IMAP\Facades\Client;

include('C:\xampp\htdocs\MLM\vendor\detectlanguage\detectlanguage\lib\detectlanguage.php');
use \DetectLanguage\DetectLanguage;

class IncomingController extends Controller
{

    private $client;
    protected $total_imported;
    private $moved_items = 0;

	public function __construct(){
		$this->middleware('auth');
		DetectLanguage::SetApiKey("a0628e1e438b9968ea9d098d2e14a278"); 	

		//Connect to imap client
		$this->client = Client::account('default');
		$this->client->connect();

		ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '1200');
	}

	public function getFolderListObj(){
		return $this->client->getFolders(false);
	}

	public function getFolderObj($folder_name){
		foreach($this->getFolderListObj() as $folder){
			if($folder->fullName == $folder_name){
				return $folder;
			}
		}
		return false;
	}

	public function moveToFolder($mail_item, $folder_name){
	    $this->moved_items++;
		return $mail_item->move($folder_name);
	}

	public function createFolder($folder_name){
		return $this->client->createFolder($this->getFolderObj($folder_name)->path);
	}

	public function getFolderMailItems($folder_name){
		return $this->getFolderObj($folder_name)->getMessages();
	}

	public function createLead($data){
		return Lead::create($data);
	}

	public function detectLanguage($text){ 
		$languageDetected = DetectLanguage::detect($text);
		return isset($languageDetected[0]->language) ? $languageDetected[0]->language : "";
	}

	public function deleteMovedItems(){
		$this->client->expunge(true);
	}

    public function count(){
        return $this->moved_items;
    }

	public function __destruct(){
	    if($this->moved_items){
            $this->deleteMovedItems();
            $this->client->disconnect();
        }
	}

}
