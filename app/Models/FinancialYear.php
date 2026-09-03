<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Service;
use App\Models\Client;

class FinancialYear extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'year_label',
    ];

    /**
     * Services belonging to this financial year.
     */
    public function services()
    {
        return $this->hasMany(Service::class);
    }
}
?>
