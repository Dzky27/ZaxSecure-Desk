<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\IncidentsController as AdminIncidentsController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\CategoriesController;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\Admin\ActivityLogsController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\User\IncidentsController as UserIncidentsController;
use App\Http\Controllers\User\ProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| All navigation links use Laravel named routes.
|
*/

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('index');

// Authentication routes (public: accessible by guests)
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('auth.login');
Route::post('/login', [LoginController::class, 'login'])->name('auth.login.store');
Route::post('/logout', [LoginController::class, 'logout'])->name('auth.logout')->middleware('auth');
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('auth.register');
Route::post('/register', [RegisterController::class, 'register'])->name('auth.register.store');

// Password reset routes (public)
Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('auth.forgot-password');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');
Route::get('/password-reset/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [ForgotPasswordController::class, 'reset'])->name('password.update');

// Admin routes (authenticated AND administrator role enforced on the server)
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/incidents', [AdminIncidentsController::class, 'index'])->name('incidents.index');
    Route::get('/incidents/create', [AdminIncidentsController::class, 'create'])->name('incidents.create');
    Route::post('/incidents', [AdminIncidentsController::class, 'store'])->name('incidents.store');
    Route::get('/incidents/{incident}', [AdminIncidentsController::class, 'show'])->name('incidents.show');
    Route::get('/incidents/{incident}/edit', [AdminIncidentsController::class, 'edit'])->name('incidents.edit');
    Route::put('/incidents/{incident}', [AdminIncidentsController::class, 'update'])->name('incidents.update');
    Route::delete('/incidents/{incident}', [AdminIncidentsController::class, 'destroy'])->name('incidents.destroy');
    Route::post('/incidents/{incident}/attachments', [AttachmentController::class, 'store'])->name('incidents.attachments.store');
    Route::delete('/incidents/{incident}/attachments/{attachment}', [AttachmentController::class, 'destroy'])->name('incidents.attachments.destroy');
    Route::get('/incidents/{incident}/attachments/{attachment}/download', [AttachmentController::class, 'download'])->name('incidents.attachments.download');

    Route::get('/users', [UsersController::class, 'index'])->name('users.index');
    Route::post('/users', [UsersController::class, 'store'])->name('users.store');
    Route::patch('/users/{user}/toggle-status', [UsersController::class, 'toggleStatus'])->name('users.toggle-status');

    Route::get('/categories', [CategoriesController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoriesController::class, 'store'])->name('categories.store');
    Route::put('/categories/{category}', [CategoriesController::class, 'update'])->name('categories.update');
    Route::patch('/categories/{category}/toggle-status', [CategoriesController::class, 'toggleStatus'])->name('categories.toggle-status');
    Route::delete('/categories/{category}', [CategoriesController::class, 'destroy'])->name('categories.destroy');

    Route::get('/reports', [ReportsController::class, 'index'])->name('reports');
    Route::get('/activity-logs', [ActivityLogsController::class, 'index'])->name('activity-logs');
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::put('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.profile');
});

// User routes (authenticated)
Route::prefix('user')->name('user.')->middleware(['auth', 'role:user'])->group(function () {
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
    Route::get('/incidents', [UserIncidentsController::class, 'index'])->name('incidents.index');
    Route::get('/incidents/create', [UserIncidentsController::class, 'create'])->name('incidents.create');
    Route::post('/incidents', [UserIncidentsController::class, 'store'])->name('incidents.store');
    Route::get('/incidents/{incident}', [UserIncidentsController::class, 'show'])->name('incidents.show');
    Route::get('/incidents/{incident}/edit', [UserIncidentsController::class, 'edit'])->name('incidents.edit');
    Route::put('/incidents/{incident}', [UserIncidentsController::class, 'update'])->name('incidents.update');
    Route::delete('/incidents/{incident}', [UserIncidentsController::class, 'destroy'])->name('incidents.destroy');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    // User attachment routes
    Route::post('/incidents/{incident}/attachments', [AttachmentController::class, 'store'])->name('attachments.store');
    Route::delete('/incidents/{incident}/attachments/{attachment}', [AttachmentController::class, 'destroy'])->name('attachments.destroy');
    Route::get('/incidents/{incident}/attachments/{attachment}/download', [AttachmentController::class, 'download'])->name('attachments.download');
});
