<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClinicaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RegularUserController;
use Illuminate\Support\Facades\Route;

// Rutas de usuarios normales
Route::get('/', [AuthController::class, 'showLogin'])->name('login.show');
Route::get('/login', [AuthController::class, 'showLogin']);
Route::post('/login', [AuthController::class, 'login'])->name('login')->middleware('throttle:10,1');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard')
    ->middleware('auth.session');
Route::get('/ver-pdf/{id_clinica}/{id_laudo}', [
    DashboardController::class,
    'verPDF',
])->name('ver.pdf')->middleware('auth.session');
Route::get('/serve-pdf/{id_clinica}/{id_laudo}', [
    DashboardController::class,
    'servePDF',
])->name('serve.pdf')->middleware('auth.session');
Route::get('/download-pdf/{id_clinica}/{id_laudo}', [
    DashboardController::class,
    'downloadPDF',
])->name('download.pdf')->middleware('auth.session');

// Rutas de administradores
Route::get('/administrar', [AdminAuthController::class, 'showLogin'])->name(
    'admin.login.show',
);
Route::post('/administrar/login', [AdminAuthController::class, 'login'])->name(
    'admin.login',
)->middleware('throttle:10,1');
Route::post('/administrar/logout', [
    AdminAuthController::class,
    'logout',
])->name('admin.logout');
Route::get('/dashboardadmin', [AdminDashboardController::class, 'index'])
    ->name('admin.dashboard')
    ->middleware('admin.auth');
Route::get('/admin/ver-pdf/{id_clinica}/{id_laudo}', [
    AdminDashboardController::class,
    'verPDF',
])
    ->name('admin.ver.pdf')
    ->middleware('admin.auth');
Route::get('/admin/serve-pdf/{id_clinica}/{id_laudo}', [
    AdminDashboardController::class,
    'servePDF',
])
    ->name('admin.serve.pdf')
    ->middleware('admin.auth');
Route::get('/admin/download-pdf/{id_clinica}/{id_laudo}', [
    AdminDashboardController::class,
    'downloadPDF',
])
    ->name('admin.download.pdf')
    ->middleware('admin.auth');
Route::delete('/admin/delete-laudo/{id_clinica}/{id_laudo}', [
    AdminDashboardController::class,
    'deleteLaudo',
])
    ->name('admin.delete.laudo')
    ->middleware('admin.auth');

// ===================================================================
// CRUD: Administración de Administradores
// ===================================================================
Route::prefix('admin/admins')
    ->name('admin.admins.')
    ->middleware('admin.auth')
    ->group(function () {
        Route::get('/', [AdminUserController::class, 'index'])->name('index');
        Route::get('/create', [AdminUserController::class, 'create'])->name(
            'create',
        );
        Route::post('/', [AdminUserController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [AdminUserController::class, 'edit'])->name(
            'edit',
        );
        Route::put('/{id}', [AdminUserController::class, 'update'])->name(
            'update',
        );
        Route::delete('/{id}', [AdminUserController::class, 'destroy'])->name(
            'destroy',
        );
    });

// ===================================================================
// CRUD: Administración de Usuarios Normales
// ===================================================================
Route::prefix('admin/usuarios')
    ->name('admin.usuarios.')
    ->middleware('admin.auth')
    ->group(function () {
        Route::get('/', [RegularUserController::class, 'index'])->name('index');
        Route::get('/create', [RegularUserController::class, 'create'])->name(
            'create',
        );
        Route::post('/', [RegularUserController::class, 'store'])->name(
            'store',
        );
        Route::get('/{id}/edit', [RegularUserController::class, 'edit'])->name(
            'edit',
        );
        Route::put('/{id}', [RegularUserController::class, 'update'])->name(
            'update',
        );
        Route::delete('/{id}', [RegularUserController::class, 'destroy'])->name(
            'destroy',
        );
    });

// ===================================================================
// CRUD: Administración de Clínicas
// ===================================================================
Route::prefix('admin/clinicas')
    ->name('admin.clinicas.')
    ->middleware('admin.auth')
    ->group(function () {
        Route::get('/', [ClinicaController::class, 'index'])->name('index');
        Route::get('/create', [ClinicaController::class, 'create'])->name(
            'create',
        );
        Route::post('/', [ClinicaController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [ClinicaController::class, 'edit'])->name(
            'edit',
        );
        Route::put('/{id}', [ClinicaController::class, 'update'])->name(
            'update',
        );
        Route::delete('/{id}', [ClinicaController::class, 'destroy'])->name(
            'destroy',
        );
    });

