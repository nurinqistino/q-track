<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StaffQueueController;
use App\Http\Controllers\VisitorController;
use App\Http\Controllers\VisitorQueueController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CounterController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\StaffController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Visitor dashboard (home)
Route::get('/', [VisitorController::class, 'dashboard'])->name('home');
Route::get('dashboard-stats', [VisitorController::class, 'dashboardStats'])->name('visitor.dashboard.stats');

// Visitor (no auth)
Route::get('services', [VisitorController::class, 'services'])->name('visitor.services');
Route::post('queue', [VisitorQueueController::class, 'store'])->name('queue.store');
Route::get('queue/{queue}', [VisitorQueueController::class, 'show'])->name('queue.display');

// Public display board
Route::get('board', [BoardController::class, 'index'])->name('board');
Route::get('board/data', [BoardController::class, 'data'])->name('board.data');

// Authentication
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('dashboard', fn () => redirect()->route(auth()->user()->role === 'admin' ? 'admin.dashboard' : 'staff.dashboard'))->name('dashboard');

    // Staff
    Route::middleware('role.staff')->prefix('staff')->name('staff.')->group(function () {
        Route::get('/', [StaffQueueController::class, 'index'])->name('dashboard');
        Route::get('profile', [ProfileController::class, 'edit'])->name('profile');
        Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::post('queue/call-next', [StaffQueueController::class, 'callNext'])->name('queue.call-next');
        Route::post('queue/{queue}/complete', [StaffQueueController::class, 'complete'])->name('queue.complete');
        Route::post('queue/{queue}/skip', [StaffQueueController::class, 'skip'])->name('queue.skip');
        Route::post('status-toggle', [StaffQueueController::class, 'toggleStatus'])->name('status.toggle');
    });

    // Admin
    Route::middleware('role.admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('dashboard/data', [AdminController::class, 'dashboardData'])->name('dashboard.data');
        Route::post('queue/assign', [AdminController::class, 'assignCounter'])->name('queue.assign');
        Route::resource('services', ServiceController::class)->except(['show']);
        Route::resource('counters', CounterController::class)->except(['show']);
        Route::resource('staff', StaffController::class)->except(['show']);
        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/pdf', [ReportController::class, 'pdf'])->name('reports.pdf');
    });
});
