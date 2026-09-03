<?php

namespace App\Services;

use App\Models\Service;
use App\Models\DocumentChecklist;
use App\Models\DocumentChecklistItem;

class DocumentChecklistService
{
    /**
     * Generate or fetch the document checklist for a given Service instance.
     */
    public function generateForService(Service $service): DocumentChecklist
    {
        // If service already has an active checklist, return it
        if ($service->checklist) {
            return $service->checklist;
        }

        $tenantId = $service->tenant_id;
        $serviceTypeStr = is_object($service->type) ? $service->type->value : $service->type;

        // Look for tenant template checklist
        $template = DocumentChecklist::where('tenant_id', $tenantId)
            ->where('service_type', $serviceTypeStr)
            ->where('is_template', true)
            ->first();

        // Create specific checklist instance for this service
        $checklist = DocumentChecklist::create([
            'tenant_id' => $tenantId,
            'service_id' => $service->id,
            'service_type' => $serviceTypeStr,
            'title' => strtoupper($serviceTypeStr) . ' Document Checklist - ' . ($service->financialYear ? $service->financialYear->year_label : ''),
            'description' => 'Checklist for ' . $service->client->name,
            'is_template' => false,
        ]);

        if ($template) {
            foreach ($template->items as $item) {
                DocumentChecklistItem::create([
                    'tenant_id' => $tenantId,
                    'document_checklist_id' => $checklist->id,
                    'name' => $item->name,
                    'description' => $item->description,
                    'is_required' => $item->is_required,
                    'document_type' => $item->document_type,
                    'sort_order' => $item->sort_order,
                    'status' => 'pending',
                ]);
            }
        }

        return $checklist;
    }
}
