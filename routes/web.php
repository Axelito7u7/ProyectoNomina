<?php

use App\Http\Controllers\daily_production_report_controller;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\production_period_controller;


// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', function () {
    return view("welcome");
});

// Route::get('/Produccion_del_dia', function () {//nombre que quieras que tenga la url
//     return view('daily_production_report');//se pone donde te va llevar
// });


Route::get('/production_period',[production_period_controller::class, 'viewProductionPeriod'] );

Route::get('/Produccion_del_dia',[daily_production_report_controller::class, 'index'] );
