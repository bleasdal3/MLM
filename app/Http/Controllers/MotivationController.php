<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MotivationController extends Controller
{
    private $motivation;

    public function rightmoveReasonForBuying($value){
        switch ($value){
            case "Relocation":
                $this->motivation = "Lifestyle - Permanent Relocation";
                break;
            case "Second Home":
                $this->motivation = "Lifestyle - Holiday Home";
                break;
            default:
                $this->motivation = null;
        }
        return $this;
    }

    public function getMotivation(){
        return $this->motivation;
    }


}
