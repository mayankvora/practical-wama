<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [EmployeeController::class, 'index'])->name('employee.index');

Route::get('employee', [EmployeeController::class,'index'])->name('employee.index');
Route::post('employee', [EmployeeController::class,'store'])->name('employee.store');
Route::post('get-employee', [EmployeeController::class,'getEmployee'])->name('get.employee');
Route::post('updateEmployee', [EmployeeController::class,'updateEmployee'])->name('employee.update');
Route::delete('delete-employee', [EmployeeController::class,'deleteEmployee'])->name('employee.delete');

