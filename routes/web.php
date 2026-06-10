<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;

Route::get('/insert', [StudentController::class, 'insert']);

Route::get('/update', [StudentController::class, 'update']);

Route::get('/delete', [StudentController::class, 'delete']);
