<?php

namespace App\Livewire\Invoices;

use Livewire\Component;
use App\Models\Invoice;

class Show extends Component
{
    public $invoice;

    public function mount($invoice)
    {
        $this->invoice = $invoice;
    }

    public function render()
    {
        return view('livewire.invoices.show');
    }
}
