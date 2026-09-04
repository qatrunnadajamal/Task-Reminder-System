<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\AiTaskController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\CalendarController;
Route::get('/', function () {
    return view('welcome');
});
Route::get('/dashboard/fetch', function () {
    return \App\Models\Reminder::latest()->take(5)->get();
});
//view
Route::get('/task/view/{id}', [TaskController::class, 'view']);
Route::get('/task/deleteModal/{id}', [TaskController::class, 'deleteModal']);

//acept invitation
Route::get('/task/invitation/accept/{token}', [TaskController::class, 'acceptInvitation'])->middleware('auth')->name('task.invitation.accept');

//add
Route::get('/add', [TaskController::class, 'add'])->name('add');
//AI
Route::middleware('auth')->group(function (){
    Route::post('/ai/generate-task', [AiTaskController::class,'create',])->name('ai.generate-task');
    Route::post('/ai/prepare-task-form', [AiTaskController::class,'prepareForm',])->name('ai.prepare-task-form');
});

//export
Route::get('/task/export/excel', [TaskController::class, 'exportExcel']);
Route::get('/exports/task-pdf', [TaskController::class, 'exportPDF']);

//checkbox 
Route::post('/task/complete/{id}', [TaskController::class, 'markComplete']);
Route::get('/dashboard', [TaskController::class, 'dashboard'])->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/calendar', function () {
    return view('calendar');
});
//scanner Ai 
Route::post(
    '/task/extract-description',
    [TaskController::class, 'extractDescriptionText']
)->middleware('auth')->name('task.extract-description');

Route::get('/calendar', [CalendarController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('calendar');
Route::get('/calendar/events', [CalendarController::class, 'events'])
    ->middleware(['auth', 'verified'])
    ->name('calendar.events');
Route::patch('/calendar/events/{id}', [CalendarController::class, 'update'])
    ->middleware(['auth', 'verified'])
    ->name('calendar.events.update');
    
Route::post('/task/store', [TaskController::class, 'store'])
    ->middleware('auth')
    ->name('task.store');
    
Route::get('/task', [TaskController::class, 'indexTaskManager'])
    ->middleware(['auth', 'verified'])
    ->name('task');

Route::get('/edit/{id}', [TaskController::class, 'edit'])
    ->name('edit');
Route::post('/update/{id}', [TaskController::class, 'update'])
    ->name('update');
Route::get('/today', [TaskController::class, 'today'])
    ->middleware(['auth', 'verified'])
    ->name('today');
    
//softdelete 
Route::delete('/delete/{id}', [TaskController::class, 'delete']);
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
//markoverdue
Route::post('/task/overdue/{id}', [TaskController::class, 'markOverdue']);

// notification
Route::get('/notifications', [NotificationController::class, 'index']);
Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
Route::post('/notifications/read/{id}', [NotificationController::class, 'markAsRead']);
Route::delete('/notifications/{id}', [NotificationController::class, 'destroy']);

require __DIR__ . '/auth.php';
