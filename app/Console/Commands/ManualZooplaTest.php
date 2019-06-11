<?php 
//entry point for manual script debugging
//TOCHECK - Perhaps move to test folder?

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Import\MailFilters\RightmoveController;

$rightmoveImport = new RightmoveController;
$rightmoveImport->processMailitems();

echo "Process completed.";

/*$zooplaImport = new ZooplaController;
$zooplaImport->processMailitems();
$count = $zooplaImport.Count();
echo 'Zoopla count - ' . $count;*/






