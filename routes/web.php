<?php

use App\Http\Controllers\daily_production_report_controller;
use App\Http\Controllers\employee_controller;
use App\Http\Controllers\final_salary_payment_report_controller;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\production_period_controller;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\BiweeklyController;
use App\Http\Controllers\ProductionController;


// Route::get('/', function () {
//     return view('welcome');
// });



// Route::get('/employee', function () {
//     return view('admin.employee');
// });
// esta es para acceder a todas las url de alguna carpeta y trabajar con todo su metodos
// Route::resource('employe',employee_controller::class);

//Ruta para pdf
Route::get("/payment_details",[final_salary_payment_report_controller::class, 'GeneratePDF']);



// Route::get('/Produccion_del_dia', function () {//nombre que quieras que tenga la url
//     return view('daily_production_report');//se pone donde te va llevar
// });


Route::get('/production_period',[production_period_controller::class, 'viewProductionPeriod']);
Route::post('/save',[production_period_controller::class, 'save'] ) -> name('save');

Route::get('/',[daily_production_report_controller::class, 'View_daily_production_report']);
Route::post('/',[daily_production_report_controller::class, 'Add_daily_production_report']);

Route::get('/final_salary', [final_salary_payment_report_controller::class,'viewProductionPeriod']);



// Employee routes
Route::get('/employee', [EmployeeController::class, 'index'])->name('employee.index');
Route::post('/employee', [EmployeeController::class, 'store'])->name('employee.store');
Route::get('/employee/{employee}', [EmployeeController::class, 'show'])->name('employee.show');
Route::get('/employee/{employee}/edit', [EmployeeController::class, 'edit'])->name('employee.edit');
Route::put('/employee/{employee}', [EmployeeController::class, 'update'])->name('employee.update');
Route::delete('/employee/{employee}', [EmployeeController::class, 'destroy'])->name('employee.destroy');
// Biweekly routes
Route::get('/biweekly', [BiweeklyController::class, 'index'])->name('biweekly.index');
Route::post('/biweekly', [BiweeklyController::class, 'store'])->name('biweekly.store');
Route::get('/biweekly/{biweekly}', [BiweeklyController::class, 'show'])->name('biweekly.show');
Route::get('/biweekly/{biweekly}/edit', [BiweeklyController::class, 'edit'])->name('biweekly.edit');
Route::put('/biweekly/{biweekly}', [BiweeklyController::class, 'update'])->name('biweekly.update');
Route::post('/biweekly/{biweekly}/close', [BiweeklyController::class, 'close'])->name('biweekly.close');

// Production Stages routes
Route::get('/stages', [ProductionController::class, 'index'])->name('stages.index');
Route::post('/stages', [ProductionController::class, 'store'])->name('stages.store');
Route::get('/stages/{stage}', [ProductionController::class, 'show'])->name('stages.show');
Route::get('/stages/{stage}/edit', [ProductionController::class, 'edit'])->name('stages.edit');
Route::put('/stages/{stage}', [ProductionController::class, 'update'])->name('stages.update');
Route::delete('/stages/{stage}', [ProductionController::class, 'destroy'])->name('stages.destroy');

// Production routes
Route::get('/production/period/{biweekly}', [ProductionController::class, 'showPeriod'])->name('production.showPeriod');

// Auth routes
Auth::routes();

