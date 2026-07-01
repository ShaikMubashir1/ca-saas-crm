<?php
use Illuminate\Support\Facades\Route;
use App\Http\Livewire\Documents\Upload;

Route::middleware(['auth', 'verified'])->group(function () {
    // Upload a document for a specific client
    Route::get('/clients/{client}/documents/upload', Upload::class)
        ->name('documents.upload');
});
