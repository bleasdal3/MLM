<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\ExportController;

class ExportToSalesforce extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export:salesforce';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Exports all pending leads to Salesforce';

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
        $export = new ExportController;
        $export->pendingToWebToLead();
        $this->info($export->count() . " item(s) sent");
    }
}
