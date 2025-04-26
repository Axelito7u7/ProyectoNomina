<?php

use App\Http\Controllers\daily_production_report_controller;
use App\Http\Controllers\employee_controller;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});

// Route::get('/employee', function () {
//     return view('admin.employee');
// });
// esta es para acceder a todas las url de alguna carpeta y trabajar con todo su metodos
// Route::resource('employe',employee_controller::class);

Route::get('/employee',[employee_controller::class,'list']);

// Route::get('/Produccion_del_dia', function () {//nombre que quieras que tenga la url
//     return view('daily_production_report');//se pone donde te va llevar
// });

Route::get('/daily_production_report',[daily_production_report_controller::class, 'View_daily_production_report'] );