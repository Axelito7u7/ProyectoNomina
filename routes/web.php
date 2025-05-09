<?php

use App\Http\Controllers\daily_production_report_controller;
use App\Http\Controllers\employee_controller;
use App\Http\Controllers\final_salary_payment_report_controller;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\production_period_controller;


// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', function () {
    return view("welcome");
});

// Route::get('/employee', function () {
//     return view('admin.employee');
// });
// esta es para acceder a todas las url de alguna carpeta y trabajar con todo su metodos
// Route::resource('employe',employee_controller::class);

//Ruta para pdf
Route::get("/payment_details",[final_salary_payment_report_controller::class, 'GeneratePDF']);
Route::get('/employee',[employee_controller::class,'list']);

// Route::get('/Produccion_del_dia', function () {//nombre que quieras que tenga la url
//     return view('daily_production_report');//se pone donde te va llevar
// });


Route::get('/production_period',[production_period_controller::class, 'viewProductionPeriod']);
Route::post('/save',[production_period_controller::class, 'save'] ) -> name('save');

Route::get('/daily_production',[daily_production_report_controller::class, 'View_daily_production_report']);
Route::post('/daily_production',[daily_production_report_controller::class, 'Add_daily_production_report']);

Route::get('/final_salary', [final_salary_payment_report_controller::class,'View_final_salary']);
