<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
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

//Rutas mediante permisos
//Administración de cuentas de usuario
Route::resource('manage-account', UserAccountController::class)->only(['create', 'store'])->middleware('auth', 'permission:create user accounts');
Route::resource('manage-account', UserAccountController::class)->only(['index', 'show'])->middleware('auth', 'permission:view user accounts');
Route::resource('manage-account', UserAccountController::class)->only(['edit', 'update'])->middleware('auth', 'permission:edit user accounts')->parameters(['manage-account' => 'user']);
Route::resource('manage-account', UserAccountController::class)->only(['destroy'])->middleware('auth', 'permission:delete user accounts')->parameters(['manage-account' => 'user']);

//Proyectos
Route::put('projects/{project}/update-date', [ProjectController::class, 'updateDate'])->name('projects.update-date')->middleware('auth', 'permission:edit projects');
Route::resource('projects', ProjectController::class)->only(['create', 'store'])->middleware('auth', 'permission:create projects');
Route::resource('projects', ProjectController::class)->only(['index', 'show'])->middleware('auth', 'permission:view projects');
Route::resource('projects', ProjectController::class)->only(['edit', 'update'])->middleware('auth', 'permission:edit projects');
Route::resource('projects', ProjectController::class)->only(['destroy'])->middleware('auth', 'permission:delete projects');

//Empresas
Route::get('companies/join', [CompanyController::class, 'joinCompany'])->name('companies.join')->middleware('auth', 'permission:join companies');
Route::post('companies/check-code', [CompanyController::class, 'checkCode'])->name('companies.checkCode')->middleware('auth', 'permission:check code companies');
Route::get('companies/redirect', [CompanyController::class, 'redirectTo'])->name('companies.redirect')->middleware('auth', 'permission:redirect companies');
Route::get('companies/{company}/members', [CompanyController::class, 'listMember'])->name('companies.listMember')->middleware('auth', 'permission:list company members');
Route::delete('companies/{company}/leave/{user}', [CompanyController::class, 'leaveCompany'])->name('companies.leave')->middleware('auth', 'permission:leave company');
Route::resource('companies', CompanyController::class)->only(['create', 'store'])->middleware('auth', 'permission:create companies');
Route::resource('companies', CompanyController::class)->only(['index', 'show'])->middleware('auth', 'permission:view companies');
Route::resource('companies', CompanyController::class)->only(['edit', 'update'])->middleware('auth', 'permission:edit companies');
Route::resource('companies', CompanyController::class)->only(['destroy'])->middleware('auth', 'permission:delete companies');

