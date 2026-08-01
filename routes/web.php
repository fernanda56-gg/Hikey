<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\MyAccountController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\NotificationSeenController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectTeamController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\UserAccountController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserProfileImgController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Rutas de autentificación
Route::get('/', [AuthController::class, 'create'])->name('login');
Route::post('login', [AuthController::class, 'store'])->name('login.store');
Route::delete('logout', [AuthController::class, 'destroy'])->name('logout');
Route::get('home', [AuthController::class, 'show'])->name('inicio')->middleware(['auth', 'verified']);

//Rutas para restablecer contraseña
Route::get('/forgot-password', [ResetPasswordController::class, 'showForm'])->name('password.request')->middleware('guest');
Route::post('/forgot-password', [ResetPasswordController::class, 'sendEmail'])->name('password.email')->middleware('guest');
Route::get('/reset-password/{token}', [ResetPasswordController::class, 'resetPassword'])->name('password.reset')->middleware('guest');
Route::post('/reset-password', [ResetPasswordController::class, 'updatePassword'])->name('password.update')->middleware('guest');

//Rutas para registro de usuarios
Route::get('register', [UserController::class, 'create'])->name('register');
Route::post('register', [UserController::class, 'store'])->name('register.store');

//Rutas de verificación de correo
Route::get('/email/verify', function () {
    return inertia('Auth/VerifyEmailPage');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect()->route('inicio')->with('success', 'Correo verificado');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return redirect()->back()->with('success', 'Enlace enviado!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

//Rutas mediante permisos
//Administración de cuentas de usuario
Route::get('manage-account/trash', [UserAccountController::class, 'trash'])->name('manage-account.trash')->middleware(['auth', 'verified']);
Route::get('/manage-account/{user}/recover', [UserAccountController::class, 'recover'])->name('manage-account.recover')->middleware(['auth', 'verified'])->withTrashed();
Route::resource('manage-account', UserAccountController::class)->only(['create', 'store'])->middleware(['auth', 'verified']);
Route::resource('manage-account', UserAccountController::class)->only(['index'])->middleware(['auth', 'verified']);
Route::resource('manage-account', UserAccountController::class)->only(['edit', 'update'])->middleware(['auth', 'verified'])->parameters(['manage-account' => 'user']);
Route::delete('/manage-account/{user}', [UserAccountController::class, 'destroy'])->name('manage-account.destroy')->middleware(['auth', 'verified'])->withTrashed();

//Proyectos
Route::put('projects/{project}/update-date', [ProjectController::class, 'updateDate'])->name('projects.update-date')->middleware(['auth', 'verified']);
Route::get('projects/trash', [ProjectController::class, 'trash'])->name('projects.trash')->middleware(['auth', 'verified']);
Route::get('/projects/{project}/recover', [ProjectController::class, 'recover'])->middleware(['auth', 'verified'])->withTrashed()->name('projects.recover');
Route::resource('projects', ProjectController::class)->only(['create', 'store'])->middleware(['auth', 'verified']);
Route::resource('projects', ProjectController::class)->only(['index', 'show'])->middleware(['auth', 'verified']);
Route::resource('projects', ProjectController::class)->only(['edit', 'update'])->middleware(['auth', 'verified']);
Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->withTrashed()->name('projects.destroy')->middleware(['auth', 'verified']);

//Empresas
Route::post('companies/check-code', [CompanyController::class, 'checkCode'])->name('companies.checkCode')->middleware(['auth', 'verified']);
Route::post('/companies/{company}/send-invitation', [CompanyController::class, 'sendInvitation'])->name('companies.sendInvitation')->middleware(['auth', 'verified']);
Route::get('companies/redirect', [CompanyController::class, 'redirectTo'])->name('companies.redirect')->middleware(['auth', 'verified']);
Route::get('companies/{company}/members', [CompanyController::class, 'listMember'])->name('companies.listMember')->middleware(['auth', 'verified']);
Route::delete('companies/{company}/leave/{user}', [CompanyController::class, 'leaveCompany'])->name('companies.leave')->middleware(['auth', 'verified']);
Route::resource('companies', CompanyController::class)->only(['create', 'store'])->middleware(['auth', 'verified']);
Route::resource('companies', CompanyController::class)->only(['index', 'show'])->middleware(['auth', 'verified']);
Route::resource('companies', CompanyController::class)->only(['edit', 'update'])->middleware(['auth', 'verified']);
Route::resource('companies', CompanyController::class)->only(['destroy'])->middleware(['auth', 'verified']);

//Clientes
Route::get('/projects/{project}/clients', [ClientController::class, 'create'])->name('clients.create')->middleware(['auth', 'verified']);
Route::get('/clients/{client}/projects', [ClientController::class, 'clientProjects'])->name('clients.projects')->middleware(['auth', 'verified']);
Route::get('/projects/{project}/clients/assign-client', [ClientController::class, 'assignClient'])->name('clients.projects.assign')->middleware(['auth', 'verified']);
Route::post('/projects/{project}/clients', [ClientController::class, 'attach'])->name('clients.projects.attach')->middleware(['auth', 'verified']);
Route::delete('/clients/{client}/{project}/projects', [ClientController::class, 'detach'])->name('clients.projects.detach')->middleware(['auth', 'verified']);
Route::get('clients/trash', [ClientController::class, 'trash'])->name('clients.trash')->middleware(['auth', 'verified']);
Route::get('/clients/{client}/recover', [ClientController::class, 'recover'])->middleware(['auth', 'verified'])->withTrashed()->name('clients.recover');
Route::resource('clients', ClientController::class)->only([ 'store'])->middleware(['auth', 'verified']);
Route::resource('clients', ClientController::class)->only(['index', 'show'])->middleware(['auth', 'verified']);
Route::resource('clients', ClientController::class)->only(['edit', 'update'])->middleware(['auth', 'verified']);
Route::resource('clients', ClientController::class)->only(['destroy'])->withTrashed()->middleware(['auth', 'verified']);

//Notificaciones
Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index')->middleware(['auth', 'verified']);
Route::put('notifications/{notification}/seen', [NotificationSeenController::class, '__invoke'])->name('notification.seen')->middleware(['auth', 'verified']);
Route::delete('notifications', [NotificationController::class, 'destroy'])->name('notifications.delete')->middleware(['auth', 'verified']);

//Ajustes a cuenta de usuario
Route::get('my-account/{user}/settings', [MyAccountController::class, 'index'])->name('my-account.index')->middleware(['auth', 'verified']);
Route::put('my-account/{user}/edit', [UserController::class, 'update'])->name('my-account.edit-account')->middleware(['auth', 'verified']);
Route::put('my-account/{user}/update-password', [MyAccountController::class, 'update'])->name('my-account.update-password')->middleware(['auth', 'verified']);
Route::delete('my-account/{user}/delete', [UserController::class, 'destroy'])->name('my-account.delete-account')->middleware(['auth', 'verified']);

//Img de perfil de usuario
Route::post('my-account/{user}/profile-photo', [UserProfileImgController::class, 'store'])->name('my-account.profile-photo')->middleware(['auth', 'verified']);

//Documentación
Route::middleware(['docs.rol'])->group(function () {
    Route::get('/docs', fn() => null);
});

// Equipos
Route::get('projects/{project}/team/available-members', [ProjectTeamController::class, 'index'])->name('project-team.index')->middleware(['auth', 'verified']);
Route::post('projects/{project}/team', [ProjectTeamController::class, 'store'])->name('project-team.store')->middleware(['auth', 'verified']);
Route::patch('projects/{project}/team/{user}/update-role', [ProjectTeamController::class, 'update'])->name('project-team.update-role')->middleware(['auth', 'verified']);
Route::put('projects/{project}/team/{user}/remove-leader', [ProjectTeamController::class, 'removeLeader'])->name('project-team.remove-leader')->middleware(['auth', 'verified']);
Route::delete('projects/{project}/team/{user}', [ProjectTeamController::class, 'destroy'])->name('project-team.destroy')->middleware(['auth', 'verified']);
