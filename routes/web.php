<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\RegisterController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return inertia('Login/LoginPage');
});

Route::get('/register', function () {
    return inertia('Login/RegisterPage', [RegisterController::class, 'index']);
});

Route::get('/home', function () {
    return inertia('Home/HomePage', [RegisterController::class, 'show']);
});

Route::resource('projects', ProjectController::class);

Route::get('login', [AuthController::class, 'create'])->name('login');
//Route::post('login', [AuthController::class, 'store'])->name('login');
//Route::delete('logout', [AuthController::class], 'delete')->name('logout');
