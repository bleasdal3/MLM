<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Http\Controllers\Import\MailFilters\RightmoveController;
use App\Http\Controllers\Import\MailFilters\KyeroController;
use App\Http\Controllers\Import\MailFilters\APlaceInTheSunController;

class ImportFromInbox extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:inbox';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports all recognisable mail items into leads ready for export.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $rightmoveImport = new RightmoveController;
        $rightmoveImport->processMailitems();

        $kyeroImport = new KyeroController;
        $kyeroImport->processMailItems();

        $aPlaceInTheSunImport = new APlaceInTheSunController;
        $aPlaceInTheSunImport->processMailItems();

        $count = $rightmoveImport->count() + $kyeroImport->count() + $aPlaceInTheSunImport->count();
        $this->info($count . " item(s) found");
    }
}
