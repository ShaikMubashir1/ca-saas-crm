<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Compliance\Dashboard;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/compliance', Dashboard::class)->name('compliance.dashboard');
});
