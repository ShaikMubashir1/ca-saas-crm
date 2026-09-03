<div class="space-y-6">
    <div class="flex items-center justify-between border-b border-[#E5E5E5] pb-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-[#252525]">Practice Reports & Analytics</h1>
            <p class="text-xs text-[#737373] mt-1 font-medium">Aggregated performance reports across clients, compliance filings, staff workload, and billing revenue.</p>
        </div>
    </div>

    {{-- 3-Column Report Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Client Directory Report --}}
        <div class="bg-white p-6 rounded-2xl border border-[#E5E5E5] shadow-xs space-y-4">
            <h3 class="text-sm font-bold text-[#252525] border-b border-[#E5E5E5] pb-2">Client Portfolio Summary</h3>
            <div class="space-y-2 text-xs">
                <div class="flex justify-between">
                    <span class="text-[#737373]">Total Registered Clients:</span>
                    <span class="font-bold text-[#252525]">{{ $clientStats['total'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-[#737373]">Active Service Subscribers:</span>
                    <span class="font-bold text-emerald-700">{{ $clientStats['active'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-[#737373]">Individual / Salaried:</span>
                    <span class="font-bold text-[#252525]">{{ $clientStats['individual'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-[#737373]">Corporate / Companies:</span>
                    <span class="font-bold text-[#252525]">{{ $clientStats['company'] }}</span>
                </div>
            </div>
        </div>

        {{-- Statutory Compliance Report --}}
        <div class="bg-white p-6 rounded-2xl border border-[#E5E5E5] shadow-xs space-y-4">
            <h3 class="text-sm font-bold text-[#252525] border-b border-[#E5E5E5] pb-2">Compliance Filings Performance</h3>
            <div class="space-y-2 text-xs">
                <div class="flex justify-between">
                    <span class="text-[#737373]">Total Statutory Instances:</span>
                    <span class="font-bold text-[#252525]">{{ $complianceStats['total'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-[#737373]">Filed & Acknowledged:</span>
                    <span class="font-bold text-emerald-700">{{ $complianceStats['filed'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-[#737373]">Docs Pending from Clients:</span>
                    <span class="font-bold text-amber-600">{{ $complianceStats['docs_pending'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-[#737373]">Critical Overdue Filings:</span>
                    <span class="font-bold text-[#ED1C24]">{{ $complianceStats['overdue'] }}</span>
                </div>
            </div>
        </div>

        {{-- Financial & Revenue Report --}}
        <div class="bg-white p-6 rounded-2xl border border-[#E5E5E5] shadow-xs space-y-4">
            <h3 class="text-sm font-bold text-[#252525] border-b border-[#E5E5E5] pb-2">Revenue & Outstanding Billing</h3>
            <div class="space-y-2 text-xs font-mono">
                <div class="flex justify-between">
                    <span class="text-[#737373] font-sans">Total Invoiced Amount:</span>
                    <span class="font-bold text-[#252525]">₹{{ number_format($billingStats['total_invoiced'], 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-[#737373] font-sans">Total Collected Payments:</span>
                    <span class="font-bold text-emerald-700">₹{{ number_format($billingStats['total_paid'], 2) }}</span>
                </div>
                <div class="flex justify-between border-t border-[#E5E5E5] pt-2">
                    <span class="text-[#737373] font-sans font-bold">Outstanding Balance Due:</span>
                    <span class="font-bold text-[#ED1C24]">₹{{ number_format($billingStats['outstanding'], 2) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
