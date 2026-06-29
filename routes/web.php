<?php

use App\Http\Controllers\AttendanceCorrectionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CoachingLogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EngagementRecordController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\LeaveTypeController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrgChartController;
use App\Http\Controllers\PipRecordController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReportTypeController;
use App\Http\Controllers\TaskLogController;
use App\Http\Controllers\ShiftLogController;
use App\Http\Controllers\PasswordController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/tasks/start',          [TaskLogController::class, 'start'])->name('tasks.start');
    Route::post('/tasks/{taskLog}/stop', [TaskLogController::class, 'stop'])->name('tasks.stop');
    Route::delete('/tasks/{taskLog}',    [TaskLogController::class, 'destroy'])->name('tasks.destroy');

    Route::post('/shift/clock-in',  [ShiftLogController::class, 'clockIn'])->name('shift.in');
    Route::post('/shift/clock-out', [ShiftLogController::class, 'clockOut'])->name('shift.out');

    Route::put('/password', [PasswordController::class, 'update'])->name('password.update');
});

Route::middleware(['auth', 'role:employee'])->prefix('member')->name('member.')->group(function () {
    Route::get('/leaves',                        [LeaveRequestController::class, 'memberIndex'])->name('leaves.index');
    Route::get('/leaves/file',                   [LeaveRequestController::class, 'create'])->name('leaves.create');
    Route::post('/leaves',                       [LeaveRequestController::class, 'store'])->name('leaves.store');
    Route::post('/leaves/{leaveRequest}/cancel', [LeaveRequestController::class, 'cancel'])->name('leaves.cancel');

    Route::post('/notifications/read-all',            [NotificationController::class, 'markAllRead'])->name('notifications.readAll');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');

    Route::get('/coaching',                         [CoachingLogController::class, 'memberIndex'])->name('coaching.index');
    Route::get('/coaching/{coaching}',              [CoachingLogController::class, 'memberShow'])->name('coaching.show');
    Route::post('/coaching/{coaching}/acknowledge', [CoachingLogController::class, 'acknowledge'])->name('coaching.acknowledge');

    // ── Attendance Correction Requests ────────────────────────────────────────
    Route::get('/corrections',        [AttendanceCorrectionController::class, 'index'])->name('corrections.index');
    Route::get('/corrections/create', [AttendanceCorrectionController::class, 'create'])->name('corrections.create');
    Route::post('/corrections',       [AttendanceCorrectionController::class, 'store'])->name('corrections.store');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    // ── Members ───────────────────────────────────────────────────────────────
    Route::get('/members',                  [MemberController::class, 'index'])->name('members.index');
    Route::post('/members',                 [MemberController::class, 'store'])->name('members.store');
    Route::put('/members/{member}',         [MemberController::class, 'update'])->name('members.update');
    Route::post('/members/{member}/toggle', [MemberController::class, 'toggle'])->name('members.toggle');

    // ── Facilities ────────────────────────────────────────────────────────────
    Route::get('/facilities',                    [FacilityController::class, 'index'])->name('facilities.index');
    Route::post('/facilities',                   [FacilityController::class, 'store'])->name('facilities.store');
    Route::put('/facilities/{facility}',         [FacilityController::class, 'update'])->name('facilities.update');
    Route::post('/facilities/{facility}/toggle', [FacilityController::class, 'toggle'])->name('facilities.toggle');
    Route::delete('/facilities/{facility}',      [FacilityController::class, 'destroy'])->name('facilities.destroy');

    // ── Report Types ──────────────────────────────────────────────────────────
    Route::get('/report-types',                      [ReportTypeController::class, 'index'])->name('report-types.index');
    Route::post('/report-types',                     [ReportTypeController::class, 'store'])->name('report-types.store');
    Route::put('/report-types/{reportType}',         [ReportTypeController::class, 'update'])->name('report-types.update');
    Route::post('/report-types/{reportType}/toggle', [ReportTypeController::class, 'toggle'])->name('report-types.toggle');
    Route::delete('/report-types/{reportType}',      [ReportTypeController::class, 'destroy'])->name('report-types.destroy');

    // ── Tasks ─────────────────────────────────────────────────────────────────
    Route::put('/tasks/{taskLog}',    [TaskLogController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{taskLog}', [TaskLogController::class, 'destroy'])->name('tasks.destroy');

    // ── Reports ───────────────────────────────────────────────────────────────
    Route::get('/reports',          [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/{member}', [ReportController::class, 'memberDetail'])->name('reports.member-detail');

    // ── Leave Types ───────────────────────────────────────────────────────────
    Route::get('/leave-types',                     [LeaveTypeController::class, 'index'])->name('leave-types.index');
    Route::post('/leave-types',                    [LeaveTypeController::class, 'store'])->name('leave-types.store');
    Route::put('/leave-types/{leaveType}',         [LeaveTypeController::class, 'update'])->name('leave-types.update');
    Route::post('/leave-types/{leaveType}/toggle', [LeaveTypeController::class, 'toggle'])->name('leave-types.toggle');
    Route::delete('/leave-types/{leaveType}',      [LeaveTypeController::class, 'destroy'])->name('leave-types.destroy');

    // ── Leave Requests ────────────────────────────────────────────────────────
    Route::get('/leave-requests',                         [LeaveRequestController::class, 'adminIndex'])->name('leave-requests.index');
    Route::post('/leave-requests/{leaveRequest}/approve', [LeaveRequestController::class, 'approve'])->name('leave-requests.approve');
    Route::post('/leave-requests/{leaveRequest}/reject',  [LeaveRequestController::class, 'reject'])->name('leave-requests.reject');

    // ── Attendance Correction Requests ────────────────────────────────────────
    Route::get('/corrections',                              [App\Http\Controllers\Admin\AttendanceCorrectionController::class, 'index'])->name('corrections.index');
    Route::post('/corrections/{correction}/approve',        [App\Http\Controllers\Admin\AttendanceCorrectionController::class, 'approve'])->name('corrections.approve');
    Route::post('/corrections/{correction}/reject',         [App\Http\Controllers\Admin\AttendanceCorrectionController::class, 'reject'])->name('corrections.reject');

    // ── Performance ───────────────────────────────────────────────────────────
    Route::resource('coaching',   CoachingLogController::class);
    Route::resource('pip',        PipRecordController::class);
    Route::resource('engagement', EngagementRecordController::class);

    // ── Org Chart ─────────────────────────────────────────────────────────────
    Route::post('orgchart/reorder', [OrgChartController::class, 'reorder'])->name('orgchart.reorder');
    Route::resource('orgchart', OrgChartController::class);
});
