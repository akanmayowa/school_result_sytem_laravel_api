<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Mail;
use App\Notifications\TrainingSchoolNotification;
use App\Models\User;

Route::get('auth/index', [App\Http\Controllers\TwoFactorAuthController::class, 'index'])->name('auth-verify.index');
Route::get('/', function () {   return view('welcome'); });

Route::get('logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);


