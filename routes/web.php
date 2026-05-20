<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChurnDashboardController;

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('//', [ChurnDashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard', [ChurnDashboardController::class, 'index'])->name('dashboard');
Route::post('/prediction/{id}/offer', [ChurnDashboardController::class, 'triggerOffer'])->name('prediction.offer');
