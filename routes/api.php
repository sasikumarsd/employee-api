<?php

use App\Http\Controllers\API\DepartmentController;
use App\Http\Controllers\API\EmployeeController;

Route::apiResource('departments', DepartmentController::class);
Route::apiResource('employees', EmployeeController::class);

?>