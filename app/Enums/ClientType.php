<?php

namespace App\Enums;

enum ClientType: string
{
    case Individual = 'individual';
    case Salaried = 'salaried';
    case Proprietor = 'proprietor';
    case PartnershipFirm = 'partnership_firm';
    case Company = 'company';
    case GstBusiness = 'gst_business';
    case TdsDeductor = 'tds_deductor';
    case AuditClient = 'audit_client';

    public function label(): string
    {
        return match($this) {
            self::Individual => 'Individual',
            self::Salaried => 'Salaried',
            self::Proprietor => 'Proprietor',
            self::PartnershipFirm => 'Partnership / Firm',
            self::Company => 'Company',
            self::GstBusiness => 'GST Business',
            self::TdsDeductor => 'TDS Deductor',
            self::AuditClient => 'Audit Client',
        };
    }
}
