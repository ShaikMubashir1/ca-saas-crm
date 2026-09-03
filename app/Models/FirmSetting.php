<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FirmSetting extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'firm_settings';

    protected $fillable = [
        'tenant_id',
        'firm_name',
        'legal_name',
        'email',
        'phone',
        'website',
        'address',
        'city',
        'state',
        'pin_code',
        'ca_reg_number',
        'gstin',
        'pan',
        'tan',
        'logo_path',
        'invoice_footer',
        'bank_name',
        'account_number',
        'ifsc_code',
        'upi_id',
        'default_gst_percent',
        'invoice_prefix',
    ];
}
