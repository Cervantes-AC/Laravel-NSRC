<div class="ai-provider-switcher p-4 bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-800">AI Provider Settings</h3>
        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
            {{ ucfirst($currentProvider) }}
        </span>
    </div>

    <!-- Message Alert -->
    @if ($showMessage)
        <div class="mb-4 p-3 rounded-lg {{ $messageType === 'success' ? 'bg-green-50 border border-green-200 text-green-800' : 'bg-red-50 border border-red-200 text-red-800' }}">
            {{ $message }}
        </div>
    @endif

    <!-- Provider Switcher -->
    <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-3">Select AI Provider</label>
        <div class="grid grid-cols-2 gap-3">
            @foreach ($providers as $provider)
                <button
                    wire:click="switchProvider('{{ $provider }}')"
                    class="px-4 py-2 rounded-lg font-medium transition-all duration-200 {{ $currentProvider === $provider ? 'bg-blue-600 text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                >
                    {{ ucfirst($provider) }}
                </button>
            @endforeach
        </div>
    </div>

    <!-- API Key Switcher -->
    <div class="border-t pt-4">
        <label class="block text-sm font-medium text-gray-700 mb-3">Switch API Key</label>
        <p class="text-xs text-gray-600 mb-3">
            Switch to an alternate API key for the current provider if rate limits are reached.
        </p>
        <button
            wire:click="switchApiKey"
            class="w-full px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg font-medium transition-all duration-200"
        >
            Switch to Alternate API Key
        </button>
    </div>

    <!-- Provider Info -->
    <div class="mt-6 p-3 bg-blue-50 border border-blue-200 rounded-lg">
        <p class="text-xs text-blue-800">
            <strong>Current Provider:</strong> {{ ucfirst($currentProvider) }}<br>
            <strong>Status:</strong> <span class="text-green-600">● Active</span>
        </p>
    </div>
</div>

<script>
    document.addEventListener('livewire:navigated', () => {
        Livewire.on('reset-message-after-delay', () => {
            setTimeout(() => {
                @this.set('showMessage', false);
            }, 3000);
        });
    });
</script>
