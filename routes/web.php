<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
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
Route::get('manage-account/trash', [UserAccountController::class, 'trash'])->name('manage-account.trash')->middleware('auth');
Route::get('/manage-account/{user}/recover', [UserAccountController::class, 'recover'])->name('manage-account.recover')->middleware('auth')->withTrashed();
Route::resource('manage-account', UserAccountController::class)->only(['create', 'store'])->middleware('auth');
Route::resource('manage-account', UserAccountController::class)->only(['index'])->middleware('auth');
Route::resource('manage-account', UserAccountController::class)->only(['edit', 'update'])->middleware('auth')->parameters(['manage-account' => 'user']);
Route::delete('/manage-account/{user}', [UserAccountController::class, 'destroy'])->name('manage-account.destroy')->middleware('auth')->withTrashed();

//Proyectos
Route::put('projects/{project}/update-date', [ProjectController::class, 'updateDate'])->name('projects.update-date')->middleware('auth');
Route::get('projects/trash', [ProjectController::class, 'trash'])->name('projects.trash')->middleware('auth');
Route::get('/projects/{project}/recover', [ProjectController::class, 'recover'])->middleware('auth')->withTrashed()->name('projects.recover');
Route::resource('projects', ProjectController::class)->only(['create', 'store'])->middleware('auth');
Route::resource('projects', ProjectController::class)->only(['index', 'show'])->middleware('auth');
Route::resource('projects', ProjectController::class)->only(['edit', 'update'])->middleware('auth');
Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->withTrashed()->name('projects.destroy')->middleware('auth');

//Empresas
Route::get('companies/join', [CompanyController::class, 'joinCompany'])->name('companies.join')->middleware('auth');
Route::post('companies/check-code', [CompanyController::class, 'checkCode'])->name('companies.checkCode')->middleware('auth');
Route::get('companies/redirect', [CompanyController::class, 'redirectTo'])->name('companies.redirect')->middleware('auth');
Route::get('companies/{company}/members', [CompanyController::class, 'listMember'])->name('companies.listMember')->middleware('auth');
Route::delete('companies/{company}/leave/{user}', [CompanyController::class, 'leaveCompany'])->name('companies.leave')->middleware('auth');
Route::resource('companies', CompanyController::class)->only(['create', 'store'])->middleware('auth');
Route::resource('companies', CompanyController::class)->only(['index', 'show'])->middleware('auth');
Route::resource('companies', CompanyController::class)->only(['edit', 'update'])->middleware('auth');
Route::resource('companies', CompanyController::class)->only(['destroy'])->middleware('auth');

//Clientes
Route::get('/projects/{project}/clients', [ClientController::class, 'create'])->name('clients.create')->middleware('auth');
Route::get('/clients/{client}/projects', [ClientController::class, 'clientProjects'])->name('clients.projects')->middleware('auth');
Route::get('/projects/{project}/clients/assign-client', [ClientController::class, 'assignClient'])->name('clients.projects.assign')->middleware('auth');
Route::post('/projects/{project}/clients', [ClientController::class, 'attach'])->name('clients.projects.attach')->middleware('auth');
Route::delete('/clients/{client}/{project}/projects', [ClientController::class, 'detach'])->name('clients.projects.detach')->middleware('auth');
Route::get('clients/trash', [ClientController::class, 'trash'])->name('clients.trash')->middleware('auth');
Route::get('/clients/{client}/recover', [ClientController::class, 'recover'])->middleware('auth')->withTrashed()->name('clients.recover');
Route::resource('clients', ClientController::class)->only([ 'store'])->middleware('auth');
Route::resource('clients', ClientController::class)->only(['index', 'show'])->middleware('auth');
Route::resource('clients', ClientController::class)->only(['edit', 'update'])->middleware('auth');
Route::resource('clients', ClientController::class)->only(['destroy'])->withTrashed()->middleware('auth');
