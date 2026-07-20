<?php
use Illuminate\Support\Facades\Route;
use App\Livewire\Invoices\Index;
use App\Livewire\Invoices\Create;
use App\Livewire\Invoices\Show;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/invoices', Index::class)->name('invoices.index');
    Route::get('/invoices/create', Create::class)->name('invoices.create');
    Route::get('/invoices/{invoice}', Show::class)->name('invoices.show');
});
