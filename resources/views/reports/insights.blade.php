<x-app-layout>
<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Report Insights') }}</h2>
</x-slot>

<div class="container mx-auto px-4 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <div class="lg:col-span-1">
            <div x-data="{
                currentProvider: '{{ session('ai_provider', config('ai.provider', 'groq')) }}',
                providers: ['groq', 'openrouter'],
                showMessage: false,
                message: '',
                messageType: 'success',
                switchProvider(provider) {
                    axios.post('/api/ai/provider/switch', { provider }).then(res => {
                        this.currentProvider = provider;
                        this.message = res.data.message;
                        this.messageType = 'success';
                        this.showMessage = true;
                        setTimeout(() => this.showMessage = false, 3000);
                    }).catch(err => {
                        this.message = err.response?.data?.message || 'Error switching provider';
                        this.messageType = 'error';
                        this.showMessage = true;
                        setTimeout(() => this.showMessage = false, 3000);
                    });
                },
                switchApiKey() {
                    axios.post('/api/ai/api-key/switch').then(res => {
                        this.message = res.data.message;
                        this.messageType = 'success';
                        this.showMessage = true;
                        setTimeout(() => this.showMessage = false, 3000);
                    }).catch(err => {
                        this.message = err.response?.data?.message || 'Error switching API key';
                        this.messageType = 'error';
                        this.showMessage = true;
                        setTimeout(() => this.showMessage = false, 3000);
                    });
                }
            }" class="p-4 bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">AI Provider Settings</h3>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800" x-text="currentProvider.charAt(0).toUpperCase() + currentProvider.slice(1)"></span>
                </div>

                <template x-if="showMessage">
                    <div class="mb-4 p-3 rounded-lg" :class="messageType === 'success' ? 'bg-green-50 border border-green-200 text-green-800' : 'bg-red-50 border border-red-200 text-red-800'" x-text="message"></div>
                </template>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Select AI Provider</label>
                    <div class="grid grid-cols-2 gap-3">
                        <template x-for="provider in providers" :key="provider">
                            <button @click="switchProvider(provider)" class="px-4 py-2 rounded-lg font-medium transition-all duration-200" :class="currentProvider === provider ? 'bg-blue-600 text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'" x-text="provider.charAt(0).toUpperCase() + provider.slice(1)"></button>
                        </template>
                    </div>
                </div>

                <div class="border-t pt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Switch API Key</label>
                    <p class="text-xs text-gray-600 mb-3">Switch to an alternate API key for the current provider if rate limits are reached.</p>
                    <button @click="switchApiKey()" class="w-full px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg font-medium transition-all duration-200">Switch to Alternate API Key</button>
                </div>

                <div class="mt-6 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                    <p class="text-xs text-blue-800"><strong>Current Provider:</strong> <span x-text="currentProvider.charAt(0).toUpperCase() + currentProvider.slice(1)"></span><br><strong>Status:</strong> <span class="text-green-600">● Active</span></p>
                </div>
            </div>
        </div>

        <div class="lg:col-span-3">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h1 class="text-3xl font-bold text-gray-800 mb-6">Report Insights</h1>

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

                <button id="generateBtn" class="w-full px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-all duration-200 mb-6">Generate Insights</button>

                <div id="loading" class="hidden mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex items-center">
                        <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-blue-600 mr-3"></div>
                        <span class="text-blue-800">Generating insights...</span>
                    </div>
                </div>

                <div id="insightsContainer" class="hidden">
                    <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                        <h3 class="text-lg font-semibold text-green-800 mb-2">AI Insights</h3>
                        <p class="text-sm text-gray-600 mb-3"><strong>Provider:</strong> <span id="providerName" class="font-medium"></span></p>
                        <div id="insightsContent" class="prose prose-sm max-w-none text-gray-700 whitespace-pre-wrap"></div>
                    </div>
                </div>

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

        if (!reportType) { alert('Please select a report type'); return; }

        document.getElementById('loading').classList.remove('hidden');
        document.getElementById('insightsContainer').classList.add('hidden');
        document.getElementById('errorContainer').classList.add('hidden');

        try {
            const response = await fetch('{{ route("reports.insights") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({ type: reportType, date_from: dateFrom || null, date_to: dateTo || null }),
            });
            const data = await response.json();
            document.getElementById('loading').classList.add('hidden');
            if (data.success) {
                document.getElementById('providerName').textContent = data.provider;
                document.getElementById('insightsContent').textContent = data.insights;
                document.getElementById('insightsContainer').classList.remove('hidden');
            } else {
                document.getElementById('errorMessage').textContent = data.error || 'Failed to generate insights';
                document.getElementById('errorContainer').classList.remove('hidden');
            }
        } catch (error) {
            document.getElementById('loading').classList.add('hidden');
            document.getElementById('errorMessage').textContent = error.message;
            document.getElementById('errorContainer').classList.remove('hidden');
        }
    });
</script>
</x-app-layout>
