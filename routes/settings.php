<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Settings\Firm;
use App\Livewire\Settings\Team;
use App\Livewire\Reports\Index as ReportsIndex;
use App\Livewire\AuditLog\Index as AuditLogIndex;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/settings/firm', Firm::class)->name('settings.firm');
    Route::get('/settings/team', Team::class)->name('settings.team');
    Route::get('/reports', ReportsIndex::class)->name('reports.index');
    Route::get('/settings/audit-log', AuditLogIndex::class)->name('audit.log');
});
