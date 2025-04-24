<?php

use App\Http\Controllers\USSDController;
use Illuminate\Support\Facades\Route;

Route::redirect('/dashboard', '/')->name('filament.admin.pages.dashboard');

Route::post('/ussd/callback', USSDController::class);
