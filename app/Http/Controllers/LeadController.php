<?php

namespace App\Http\Controllers;

use App\Lead;

class LeadController extends Controller
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
    	$leads = Lead::where("exported", "=", false)->get();
        return view('leads.index')
        	->with("leads", $leads);
    }
}
