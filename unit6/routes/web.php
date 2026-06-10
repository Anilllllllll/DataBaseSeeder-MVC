<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TaskController;


Route::resource('tasks', TaskController::class);
Route::put('/task/{task}/priority', [TaskController::class, 'updatePriority'])->name('task.updatePriority');



