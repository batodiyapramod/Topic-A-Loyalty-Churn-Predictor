<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChurnDashboardController;


Route::get('/members/at-risk', [ChurnDashboardController::class, 'apiEndpoint']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
