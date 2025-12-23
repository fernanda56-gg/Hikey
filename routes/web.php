<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\UserAccountController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

//Rutas de autentificación
Route::get('/', [AuthController::class, 'create'])->name('login');
Route::post('login', [AuthController::class, 'store'])->name('login.store');
Route::delete('logout', [AuthController::class, 'destroy'])->name('logout');
Route::get('home', [AuthController::class, 'show'])->name('inicio')->middleware('auth');

Route::get('register', [UserController::class, 'create'])->name('register');
Route::post('register', [UserController::class, 'store'])->name('register.store');

//Rutas para usuario rol administrador
Route::group(['middleware' => ['auth', 'role:admin']], function () {
    Route::resource('manage-account', UserAccountController::class)->parameters(['manage-account' => 'user']);
});


//Rutas para otros usuarios mediante permisos
//Proyectos
Route::resource('projects', ProjectController::class)->only(['create', 'store'])->middleware('auth', 'permission: create projects');
Route::resource('projects', ProjectController::class)->only(['index', 'show'])->middleware('auth', 'permission: view projects');
Route::resource('projects', ProjectController::class)->only(['edit', 'update'])->middleware('auth', 'permission: edit projects');
Route::resource('projects', ProjectController::class)->only(['destroy'])->middleware('auth', 'permission: delete projects');

