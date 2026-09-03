<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Documents\Upload;
use App\Http\Controllers\DocumentDownloadController;

// Public Tokenized Client Upload Portal (No auth required)
Route::get('/client-upload/{token}', \App\Livewire\Public\ClientUploadPortal::class)
    ->name('client.upload.portal')
    ->middleware('throttle:15,1');

Route::get('/portal/documents/{token}', \App\Livewire\Public\ClientUploadPortal::class)
    ->name('client.portal.documents')
    ->middleware('throttle:15,1');

Route::middleware(['auth', 'verified'])->group(function () {
    // Communication Templates
    Route::get('/communication/templates', \App\Livewire\Communication\TemplatesPage::class)
        ->name('communication.templates');

    // Central Document Vault List
    Route::get('/documents', \App\Livewire\Documents\DocumentsPage::class)
        ->name('documents.index');

    // Upload a document for a specific client
    Route::get('/clients/{client}/documents/upload', Upload::class)
        ->name('documents.upload');

    // Secure Document Download
    Route::get('/documents/{document}/download', DocumentDownloadController::class)
        ->name('documents.download');
});
