<?php

namespace App\Http\Controllers;

use App\Models\activity_log;
use App\Models\employee;
use Illuminate\Http\Request;

class production_period_controller extends Controller
{
    public function viewProductionPeriod(){
        $dbActivityLog = activity_log::all(); 
        $dbEmployee = employee::all();

        return view("production_period", compact("dbActivityLog", "dbEmployee"));
    }
}
