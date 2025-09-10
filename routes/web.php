<?php

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Registration routes
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register');

// User management routes
Route::get('/users', [UserController::class, 'index'])->name('users.index');
Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
Route::get('/users/{user}/delete', [UserController::class, 'delete'])->name('users.delete');
Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

// User import routes
Route::get('/users/import', [UserController::class, 'showImportForm'])->name('users.import');
Route::post('/users/import', [UserController::class, 'import'])->name('users.import');

// User export routes
Route::get('/users/export', [UserController::class, 'export'])->name('users.export');
Route::get('/users/exports', [UserController::class, 'listExports'])->name('users.exports');
