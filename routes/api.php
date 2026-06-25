<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ActController;

Route::get('/dashboard', [DashboardController::class, 'index']);
Route::patch('/acts/{act}', [DashboardController::class, 'updateActStatus']);
Route::post('/acts', [ActController::class, 'store']);