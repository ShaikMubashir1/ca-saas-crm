<?php

namespace App\Enums;

enum ServiceType: string
{
    case Itr = 'itr';
    case Gst = 'gst';
    case Tds = 'tds';
    case Audit = 'audit';
    case Roc = 'roc';
    case Accounting = 'accounting';
    case TaxConsultation = 'tax_consultation';

    public function label(): string
    {
        return match($this) {
            self::Itr => 'ITR',
            self::Gst => 'GST',
            self::Tds => 'TDS',
            self::Audit => 'Audit',
            self::Roc => 'ROC',
            self::Accounting => 'Accounting',
            self::TaxConsultation => 'Tax Consultation',
        };
    }
}
