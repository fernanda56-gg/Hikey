<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ManageAccounts;
use App\Http\Controllers\ManageAccountsController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

//Rutas de autentificación
Route::get('/', [AuthController::class, 'create'])->name('login');
Route::post('login', [AuthController::class, 'store'])->name('login.store');
Route::delete('logout', [AuthController::class, 'destroy'])->name('logout');
Route::get('home', [AuthController::class, 'show'])->name('inicio')->middleware('auth');

Route::get('register', [UserController::class, 'create'])->name('register');
Route::post('register', [UserController::class, 'store'])->name('register.store');

Route::get('manage-account', [ManageAccountsController::class, 'index'])->name('manage-account')->middleware('auth');

Route::resource('projects', ProjectController::class)->middleware('auth');
