<?php

use App\Http\Controllers\SmsCallbackController;
use Illuminate\Support\Facades\Route;

Route::redirect('/dashboard', '/', )->name('filament.admin.pages.dashboard');

Route::post('/sms/callback', SmsCallbackController::class);
