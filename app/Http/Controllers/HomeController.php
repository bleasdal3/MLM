<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Import\MailFilters\APlaceInTheSunController;
use App\Http\Controllers\Import\MailFilters\KyeroController;
use App\Http\Controllers\Import\MailFilters\RightmoveController;
use App\Http\Controllers\ExportController;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }

    public function home(){
        return redirect('/');
    }

    public function rightmoveImport(){
        $rightmoveImport = new RightmoveController;
        $rightmoveImport->processMailItems();
        return redirect('/')
            ->with("message", $rightmoveImport->count()." Rightmove leads imported");
    }

    public function kyeroImport(){
        $kyeroImport = new KyeroController;
        $kyeroImport->processMailItems();
        return redirect('/')
            ->with("message", $kyeroImport->count()." Kyero leads imported");
    }

    public function aPlaceInTheSunImport(){
        $aPlaceInTheSunImport = new APlaceInTheSunController;
        $aPlaceInTheSunImport->processMailItems();
        return redirect('/')
            ->with("message", $aPlaceInTheSunImport->count()." A Place In The Sun leads imported");
    }

    public function export(){
        $export = new ExportController;
        $export->pendingToWebToLead();
        return redirect('/')
            ->with("message", $export->count()." leads exported.");
    }
}
