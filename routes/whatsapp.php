<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\WhatsApp\Inbox;
use App\Livewire\WhatsApp\Broadcast;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/whatsapp', Inbox::class)->name('whatsapp.inbox');
    Route::get('/whatsapp/broadcasts', Broadcast::class)->name('whatsapp.broadcast');
});
