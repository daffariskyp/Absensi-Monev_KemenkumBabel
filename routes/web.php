<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdministrationController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TimeManagementController;
use App\Http\Controllers\UserController;
use App\Models\ProjectEmployee;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
  return redirect('login');
});


Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate'])->name('authenticate');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


Route::middleware('auth')->group(function () {
  Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
  Route::get('/dashboard/print-today', [DashboardController::class, 'printToday'])->name('dashboard.printToday');
  Route::resource('projects', ProjectController::class);
  Route::put('/projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
  Route::get('/tasks/all-task', [TaskController::class, 'allTask'])->name('tasks.allTask');
  Route::post('/tasks/{id}/update-status', [TaskController::class, 'updateStatus'])->name('tasks.updateStatus');
  Route::get('/tasks/{id}/employees', [TaskController::class, 'getEmployees'])->name('tasks.getEmployees');
  Route::get('/tasks/get-tasks', [TaskController::class, 'getTasks'])->name('tasks.getTasks');
  Route::resource('tasks', TaskController::class);
  Route::resource('activity', ActivityController::class);
  Route::resource('admin', AdminController::class);
  Route::resource('setting', SettingController::class);
  Route::resource('calendar', CalendarController::class);
  Route::resource('time-management', TimeManagementController::class);
  Route::resource('users', UserController::class);
  Route::get('/users/profile', [UserController::class, 'show'])->name('users.profile');

  // Administration (Pengajuan Izin & Cuti Non-ASN)
  Route::post('/administration/{id}/approve', [AdministrationController::class, 'approve'])->name('administration.approve');
  Route::post('/administration/{id}/reject', [AdministrationController::class, 'reject'])->name('administration.reject');
  Route::resource('administration', AdministrationController::class);

  // Attendance & Monev Non-ASN
  Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
  Route::post('/attendance/checkin', [AttendanceController::class, 'checkIn'])->name('attendance.checkin');
  Route::post('/attendance/checkout', [AttendanceController::class, 'checkOut'])->name('attendance.checkout');
  Route::get('/attendance/recap', [AttendanceController::class, 'recap'])->name('attendance.recap');
  Route::get('/attendance/print', [AttendanceController::class, 'printRecap'])->name('attendance.print');
  Route::get('/attendance/{id}/monev', [AttendanceController::class, 'getMonevDetail'])->name('attendance.monev');
  Route::get('/attendance/settings', [AttendanceController::class, 'settings'])->name('attendance.settings');
  Route::post('/attendance/settings', [AttendanceController::class, 'updateSettings'])->name('attendance.settings.update');

  Route::get('/notify', function () {notify()->success('Notification Triggered!', 'Success');return back();})->name('notify');
});
