<?php 
//entry point for manual script debugging
//TOCHECK - Perhaps move to test folder?

namespace App\Console\Commands;

//use autoload for CLI entry ##################################

include_once('C:\xampp\htdocs\MLM\bootstrap\autoload.php');

use Illuminate\Console\Command;
use App\Http\Controllers\Import\MailFilters\RightmoveController;

$rightmoveImport = new RightmoveController;
$rightmoveImport->processMailitems();

echo "Process completed.";

/*$zooplaImport = new ZooplaController;
$zooplaImport->processMailitems();
$count = $zooplaImport.Count();
echo 'Zoopla count - ' . $count;*/






