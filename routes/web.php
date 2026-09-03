<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;

// Existing base routes
Route::view('/', 'welcome');
Route::get('dashboard', DashboardController::class)->middleware(['auth', 'verified'])->name('dashboard');
Route::view('profile', 'profile')->middleware(['auth'])->name('profile');

// Module routes
require __DIR__.'/documents.php';
require __DIR__.'/tasks.php';
require __DIR__.'/clients.php';
require __DIR__.'/invoices.php';
require __DIR__.'/compliance.php';
require __DIR__.'/whatsapp.php';
require __DIR__.'/settings.php';

// Public WhatsApp Webhook Endpoint (CSRF-exempt / API)
Route::get('/webhooks/whatsapp', [\App\Http\Controllers\Webhooks\WhatsAppWebhookController::class, 'verify']);
Route::post('/webhooks/whatsapp', [\App\Http\Controllers\Webhooks\WhatsAppWebhookController::class, 'handle']);

require __DIR__.'/auth.php';
