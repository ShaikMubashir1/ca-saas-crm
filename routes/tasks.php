<?php
use Illuminate\Support\Facades\Route;
use App\Livewire\Tasks\Index;
use App\Livewire\Tasks\Create;
use App\Livewire\Tasks\Show;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/tasks', Index::class)->name('tasks.index');
    Route::get('/tasks/create', Create::class)->name('tasks.create');
    Route::get('/tasks/{task}', Show::class)->name('tasks.show');
});
