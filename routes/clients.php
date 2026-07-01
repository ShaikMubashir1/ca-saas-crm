<?php
use Illuminate\Support\Facades\Route;
use App\Http\Livewire\Clients\Index;
use App\Http\Livewire\Clients\Create;
use App\Http\Livewire\Clients\Show;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/clients', Index::class)->name('clients.index');
    Route::get('/clients/create', Create::class)->name('clients.create');
    Route::get('/clients/{client}', Show::class)->name('clients.show');
});
