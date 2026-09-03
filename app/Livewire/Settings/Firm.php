<?php

namespace App\Livewire\Settings;

use App\Models\FirmSetting;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Firm extends Component
{
    use WithFileUploads;

    public string $firm_name = '';
    public string $legal_name = '';
    public string $email = '';
    public string $phone = '';
    public string $website = '';
    public string $address = '';
    public string $city = '';
    public string $state = '';
    public string $pin_code = '';
    public string $ca_reg_number = '';
    public string $gstin = '';
    public string $pan = '';
    public string $tan = '';
    public string $bank_name = '';
    public string $account_number = '';
    public string $ifsc_code = '';
    public string $upi_id = '';
    public float $default_gst_percent = 18.00;
    public string $invoice_prefix = 'INV';
    public string $invoice_footer = '';

    public function mount(): void
    {
        $tenantId = Auth::user()->tenant_id;
        $setting = FirmSetting::where('tenant_id', $tenantId)->first();

        if ($setting) {
            $this->firm_name = $setting->firm_name ?? '';
            $this->legal_name = $setting->legal_name ?? '';
            $this->email = $setting->email ?? '';
            $this->phone = $setting->phone ?? '';
            $this->website = $setting->website ?? '';
            $this->address = $setting->address ?? '';
            $this->city = $setting->city ?? '';
            $this->state = $setting->state ?? '';
            $this->pin_code = $setting->pin_code ?? '';
            $this->ca_reg_number = $setting->ca_reg_number ?? '';
            $this->gstin = $setting->gstin ?? '';
            $this->pan = $setting->pan ?? '';
            $this->tan = $setting->tan ?? '';
            $this->bank_name = $setting->bank_name ?? '';
            $this->account_number = $setting->account_number ?? '';
            $this->ifsc_code = $setting->ifsc_code ?? '';
            $this->upi_id = $setting->upi_id ?? '';
            $this->default_gst_percent = (float)$setting->default_gst_percent;
            $this->invoice_prefix = $setting->invoice_prefix ?? 'INV';
            $this->invoice_footer = $setting->invoice_footer ?? '';
        } else {
            $this->firm_name = Auth::user()->tenant->name ?? 'My CA Firm';
        }
    }

    public function saveSettings(): void
    {
        $this->validate([
            'firm_name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'default_gst_percent' => 'required|numeric|min:0|max:100',
            'invoice_prefix' => 'required|string|max:10',
        ]);

        $tenantId = Auth::user()->tenant_id;

        FirmSetting::updateOrCreate(
            ['tenant_id' => $tenantId],
            [
                'firm_name' => $this->firm_name,
                'legal_name' => $this->legal_name,
                'email' => $this->email,
                'phone' => $this->phone,
                'website' => $this->website,
                'address' => $this->address,
                'city' => $this->city,
                'state' => $this->state,
                'pin_code' => $this->pin_code,
                'ca_reg_number' => $this->ca_reg_number,
                'gstin' => $this->gstin,
                'pan' => $this->pan,
                'tan' => $this->tan,
                'bank_name' => $this->bank_name,
                'account_number' => $this->account_number,
                'ifsc_code' => $this->ifsc_code,
                'upi_id' => $this->upi_id,
                'default_gst_percent' => $this->default_gst_percent,
                'invoice_prefix' => $this->invoice_prefix,
                'invoice_footer' => $this->invoice_footer,
            ]
        );

        session()->flash('success', 'Firm profile and billing defaults updated successfully.');
    }

    public function render()
    {
        return view('livewire.settings.firm');
    }
}
