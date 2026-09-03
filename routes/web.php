<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InternController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\DailyReportController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\Auth\Email2FAController;

// Root redirect
Route::get('/', function () {
    return redirect()->route('login');
});

// Note: Login/Register views are handled by FortifyServiceProvider
// These routes below are kept for backward compatibility with existing controllers

// Google OAuth Routes
Route::get('/auth/google', [SocialAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

// OAuth 2FA Routes (before user is logged in)
Route::get('/auth/2fa/setup', [SocialAuthController::class, 'show2FASetup'])->name('oauth.2fa.setup');
Route::post('/auth/2fa/setup', [SocialAuthController::class, 'verify2FASetup'])->name('oauth.2fa.setup.verify');
Route::get('/auth/2fa/verify', [SocialAuthController::class, 'show2FAVerify'])->name('oauth.2fa.verify');
Route::post('/auth/2fa/verify', [SocialAuthController::class, 'verify2FA'])->name('oauth.2fa.verify.submit');
Route::post('/auth/2fa/cancel', [SocialAuthController::class, 'cancel2FA'])->name('oauth.2fa.cancel');
Route::get('/auth/pending', [SocialAuthController::class, 'showPending'])->name('oauth.pending');
Route::post('/auth/check-status', [SocialAuthController::class, 'checkApprovalStatus'])->name('oauth.check-status');
Route::get('/auth/auto-login', [SocialAuthController::class, 'autoLogin'])->name('oauth.auto-login');
Route::get('/auth/complete-profile', [SocialAuthController::class, 'showCompleteProfile'])->name('oauth.complete-profile');
Route::post('/auth/complete-profile', [SocialAuthController::class, 'submitCompleteProfile'])->name('oauth.complete-profile.submit');

// Email Login 2FA Routes (for email/password login with 2FA)
Route::get('/email/2fa/verify', [Email2FAController::class, 'showVerifyForm'])->name('email.2fa.verify');
Route::post('/email/2fa/verify', [Email2FAController::class, 'verify'])->name('email.2fa.verify.submit');
Route::post('/email/2fa/cancel', [Email2FAController::class, 'cancel'])->name('email.2fa.cancel');

// Protected Routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::get('/profile/security', function () {
        return view('profile.security');
    })->name('profile.security');

    // Tasks (accessible by all authenticated users) - Index uses Livewire
    Route::get('/tasks', \App\Livewire\Tasks\TaskIndex::class)->name('tasks.index');
    Route::get('/tasks/create', [TaskController::class, 'create'])->name('tasks.create');
    Route::get('/tasks/search-interns', [TaskController::class, 'searchInterns'])->name('tasks.searchInterns');
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::get('/tasks/{task}', [TaskController::class, 'show'])->name('tasks.show');
    Route::get('/tasks/{task}/edit', [TaskController::class, 'edit'])->name('tasks.edit');
    Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
    Route::post('/tasks/{task}/status', [TaskController::class, 'updateStatus'])->middleware('throttle:30,1')->name('tasks.updateStatus');
    Route::post('/tasks/{task}/submit', [TaskController::class, 'submit'])->middleware('throttle:10,1')->name('tasks.submit');
    Route::post('/tasks/{task}/review', [TaskController::class, 'review'])->middleware('throttle:10,1')->name('tasks.review');

    // Task Assignments (Grouped Task View)
    Route::get('/task-assignments', [TaskController::class, 'assignmentsIndex'])->name('task-assignments.index');
    Route::get('/task-assignments/{taskAssignment}', [TaskController::class, 'assignmentShow'])->name('task-assignments.show');

    // Calendar View
    Route::get('/calendar', \App\Livewire\Calendar::class)->name('calendar');

    // Attendance (accessible by all authenticated users) - Index uses Livewire
    Route::get('/attendances', \App\Livewire\Attendances\AttendanceIndex::class)->name('attendances.index');
    Route::get('/attendances/create', [AttendanceController::class, 'create'])->name('attendances.create');
    Route::post('/attendances', [AttendanceController::class, 'store'])->name('attendances.store');
    Route::get('/attendances/{attendance}', [AttendanceController::class, 'show'])->name('attendances.show');
    Route::get('/attendances/{attendance}/edit', [AttendanceController::class, 'edit'])->name('attendances.edit');
    Route::put('/attendances/{attendance}', [AttendanceController::class, 'update'])->name('attendances.update');
    Route::delete('/attendances/{attendance}', [AttendanceController::class, 'destroy'])->name('attendances.destroy');
    Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn'])->middleware('throttle:5,1')->name('attendance.checkIn');
    Route::post('/attendance/check-out', [AttendanceController::class, 'checkOut'])->middleware('throttle:5,1')->name('attendance.checkOut');
    Route::post('/attendance/permission', [AttendanceController::class, 'submitPermission'])->middleware('throttle:5,1')->name('attendance.permission');

    // Notifications
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{notification}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::get('/notifications/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllRead'])->name('notifications.markAllRead');
    Route::delete('/notifications/{notification}', [\App\Http\Controllers\NotificationController::class, 'destroy'])->name('notifications.destroy');

    // Daily Report (Intern only)
   Route::get('/daily-reports', [DailyReportController::class, 'index'])->name('daily-reports.index');
   Route::post('/daily-reports', [DailyReportController::class, 'store'])->name('daily-reports.store');
   Route::get('/daily-reports/{dailyReport}', [DailyReportController::class, 'show'])->name('daily-reports.show');
   Route::put('/daily-reports/{dailyReport}', [DailyReportController::class, 'update'])->name('daily-reports.update');

    // Admin/Pembimbing only routes
    Route::middleware(['role:admin,pembimbing'])->group(function () {
        // Interns CRUD - Fully Livewire managed (index, create, edit, delete)
        Route::get('/interns', \App\Livewire\Interns\InternIndex::class)->name('interns.index');
        Route::get('/interns/create', \App\Livewire\Interns\InternForm::class)->name('interns.create');
        Route::get('/interns/{intern}', [InternController::class, 'show'])->name('interns.show');
        Route::get('/interns/{intern}/edit', \App\Livewire\Interns\InternForm::class)->name('interns.edit');

                // Reports
        Route::resource('reports', ReportController::class);
        Route::post('/reports/{report}/feedback', [ReportController::class, 'addFeedback'])->name('reports.feedback');
        Route::get('/interns/{intern}/download-report', [ReportController::class, 'downloadInternReport'])->name('interns.downloadReport');
        // Certificate
        Route::get('/interns/{intern}/certificate', [\App\Http\Controllers\CertificateController::class, 'generate'])->name('interns.certificate');

        // Daily Report Monitoring (Admin/Pembimbing)
        Route::get('/daily-reports-monitoring', [DailyReportController::class, 'monitoring'])->name('daily-reports.monitoring');

        // Assessments - Index uses Livewire
        Route::get('/assessments', \App\Livewire\Assessments\AssessmentIndex::class)->name('assessments.index');
        Route::get('/assessments/create', [AssessmentController::class, 'create'])->name('assessments.create');
        Route::post('/assessments', [AssessmentController::class, 'store'])->name('assessments.store');
        Route::get('/assessments/{assessment}', [AssessmentController::class, 'show'])->name('assessments.show');
        Route::get('/assessments/{assessment}/edit', [AssessmentController::class, 'edit'])->name('assessments.edit');
        Route::put('/assessments/{assessment}', [AssessmentController::class, 'update'])->name('assessments.update');
        Route::delete('/assessments/{assessment}', [AssessmentController::class, 'destroy'])->name('assessments.destroy');

        // Export Routes
        Route::get('/export/interns', [\App\Http\Controllers\ExportImportController::class, 'exportInterns'])->name('export.interns');
        Route::get('/export/attendances', [\App\Http\Controllers\ExportImportController::class, 'exportAttendances'])->name('export.attendances');
        Route::get('/export/tasks', [\App\Http\Controllers\ExportImportController::class, 'exportTasks'])->name('export.tasks');

        // Import Routes
        Route::get('/import/interns', [\App\Http\Controllers\ExportImportController::class, 'showImportForm'])->name('import.interns.form');
        Route::post('/import/interns', [\App\Http\Controllers\ExportImportController::class, 'importInterns'])->name('import.interns');
        Route::get('/import/template', [\App\Http\Controllers\ExportImportController::class, 'downloadTemplate'])->name('import.template');
    });

    // Admin only routes
    Route::middleware(['role:admin'])->group(function () {
        // Supervisors CRUD - Admin only
        Route::get('/supervisors', \App\Livewire\Supervisors\SupervisorIndex::class)->name('supervisors.index');
        Route::get('/supervisors/create', \App\Livewire\Supervisors\SupervisorForm::class)->name('supervisors.create');
        Route::get('/supervisors/{supervisor}/edit', \App\Livewire\Supervisors\SupervisorForm::class)->name('supervisors.edit');

        // Settings
        Route::get('/settings', [\App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [\App\Http\Controllers\SettingController::class, 'update'])->name('settings.update');

        // Daily Report - hapus (admin only)
        Route::delete('/daily-reports/{dailyReport}', [DailyReportController::class, 'destroy'])->name('daily-reports.destroy');
    });
});
