<?php

use App\Http\Controllers\Api\EmployeeController;
use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function () {
    Route::apiResource('employees', EmployeeController::class)->only(['index', 'show', 'store']);
});
