<?php

namespace App\Http\Controllers;
use App\Models\employee;
use Illuminate\Http\Request;

class employee_controller extends Controller
{
    public function list(){
        $employees = employee::all();
        return view("admin/employee",compact("employees"));
    }
    public function create(){
        return view("employee.create");
    }
}
