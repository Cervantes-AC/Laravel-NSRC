<x-app-layout>
<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Report Insights') }}</h2>
</x-slot>

<div class="container mx-auto px-4 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Sidebar with Provider Switcher -->
        <div class="lg:col-span-1">
            <livewire:ai-provider-switcher />
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-3">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h1 class="text-3xl font-bold text-gray-800 mb-6">Report Insights</h1>

                <!-- Report Type Selection -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Report Type</label>
                    <select id="reportType" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">-- Choose a report type --</option>
                        <option value="user_activity">User Activity</option>
                        <option value="transaction_summary">Transaction Summary</option>
                        <option value="audit_trail">Audit Trail</option>
                        <option value="system_usage">System Usage</option>
                        <option value="custom">Custom Report</option>
                    </select>
                </div>

                <!-- Filters -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date From</label>
                        <input type="date" id="dateFrom" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date To</label>
                        <input type="date" id="dateTo" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>

                <!-- Generate Button -->
                <button id="generateBtn" class="w-full px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-all duration-200 mb-6">
                    Generate Insights
                </button>

                <!-- Loading State -->
                <div id="loading" class="hidden mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex items-center">
                        <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-blue-600 mr-3"></div>
                        <span class="text-blue-800">Generating insights...</span>
                    </div>
                </div>

                <!-- Insights Display -->
                <div id="insightsContainer" class="hidden">
                    <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                        <h3 class="text-lg font-semibold text-green-800 mb-2">AI Insights</h3>
                        <p class="text-sm text-gray-600 mb-3">
                            <strong>Provider:</strong> <span id="providerName" class="font-medium"></span>
                        </p>
                        <div id="insightsContent" class="prose prose-sm max-w-none text-gray-700 whitespace-pre-wrap"></div>
                    </div>
                </div>

                <!-- Error Display -->
                <div id="errorContainer" class="hidden mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <h3 class="text-lg font-semibold text-red-800 mb-2">Error</h3>
                    <p id="errorMessage" class="text-red-700"></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('generateBtn').addEventListener('click', async function() {
        const reportType = document.getElementById('reportType').value;
        const dateFrom = document.getElementById('dateFrom').value;
        const dateTo = document.getElementById('dateTo').value;

        if (!reportType) {
            alert('Please select a report type');
            return;
        }

        const loading = document.getElementById('loading');
        const insightsContainer = document.getElementById('insightsContainer');
        const errorContainer = document.getElementById('errorContainer');

        loading.classList.remove('hidden');
        insightsContainer.classList.add('hidden');
        errorContainer.classList.add('hidden');

        try {
            const response = await fetch('{{ route("reports.insights") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({
                    type: reportType,
                    date_from: dateFrom || null,
                    date_to: dateTo || null,
                }),
            });

            const data = await response.json();

            loading.classList.add('hidden');

            if (data.success) {
                document.getElementById('providerName').textContent = data.provider;
                document.getElementById('insightsContent').textContent = data.insights;
                insightsContainer.classList.remove('hidden');
            } else {
                document.getElementById('errorMessage').textContent = data.error || 'Failed to generate insights';
                errorContainer.classList.remove('hidden');
            }
        } catch (error) {
            loading.classList.add('hidden');
            document.getElementById('errorMessage').textContent = error.message;
            errorContainer.classList.remove('hidden');
        }
    });
</script>
</x-app-layout>
