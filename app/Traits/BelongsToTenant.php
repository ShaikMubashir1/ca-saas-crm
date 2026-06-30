<?php

namespace App\Traits;

use App\Models\Scopes\TenantScope;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant()
    {
        static::addGlobalScope(new TenantScope);

        static::creating(function ($model) {
            if (!$model->tenant_id) {
                if (Session::has('tenant_id')) {
                    $model->tenant_id = Session::get('tenant_id');
                } elseif (Auth::check() && Auth::user()->tenant_id) {
                    $model->tenant_id = Auth::user()->tenant_id;
                }
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
