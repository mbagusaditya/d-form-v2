<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\HomeController as DashboardHomeController;

Route::middleware('auth')->get('/admin', fn () => to_route('dashboard.home'));

Route::get('/dashboard', DashboardHomeController::class)->name('dashboard.home');
