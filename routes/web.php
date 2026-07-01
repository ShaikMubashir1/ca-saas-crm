<?php

use Illuminate\Support\Facades\Route;

// Existing base routes
Route::view('/', 'welcome');
Route::view('dashboard', 'dashboard')->middleware(['auth', 'verified'])->name('dashboard');
Route::view('profile', 'profile')->middleware(['auth'])->name('profile');

// Register module route files
require __DIR__.'/documents.php';
require __DIR__.'/tasks.php';
require __DIR__.'/clients.php';
require __DIR__.'/invoices.php';

require __DIR__.'/auth.php';
