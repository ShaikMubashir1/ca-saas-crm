<x-app-layout>
    <div class="mb-6">
        <h1 class="text-3xl font-bold tracking-tight text-gray-900">Dashboard</h1>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Metric Cards -->
        <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-indigo-50 rounded-lg p-3">
                        <svg class="h-8 w-8 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Clients</dt>
                            <dd class="text-3xl font-bold text-gray-900">0</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-amber-50 rounded-lg p-3">
                        <svg class="h-8 w-8 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Pending Tasks</dt>
                            <dd class="text-3xl font-bold text-gray-900">0</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-emerald-50 rounded-lg p-3">
                        <svg class="h-8 w-8 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Completed This Month</dt>
                            <dd class="text-3xl font-bold text-gray-900">0</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white shadow-sm rounded-xl border border-gray-100 mb-8 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
            <h3 class="text-lg leading-6 font-semibold text-gray-900">Recent Activity</h3>
        </div>
        <div class="p-6">
            <p class="text-gray-500 text-sm">No recent activity found.</p>
        </div>
    </div>
</x-app-layout>
