@php
    use App\Services\AIModelService;
    
    $aiService = app(AIModelService::class);
    $currentModel = $aiService->getUserModel();
    $availableModels = $aiService->getAvailableModels();
    $modelsByProvider = $aiService->getModelsByProvider();
@endphp

<div x-data="aiModelSwitcher()" class="relative">
    {{-- Trigger Button --}}
    <button @click="open = !open" 
            class="flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-600 shadow-sm transition hover:border-slate-300 hover:text-slate-900"
            title="Switch AI Model">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5a4 4 0 100-8 4 4 0 000 8z" />
        </svg>
        <span class="hidden sm:inline text-xs">{{ $availableModels[$currentModel]['name'] ?? 'AI Model' }}</span>
        <svg class="h-4 w-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
        </svg>
    </button>

    {{-- Dropdown Menu --}}
    <div x-show="open" 
         @click.away="open = false"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="absolute right-0 mt-2 w-96 z-50 bg-white border border-slate-200 rounded-lg shadow-xl"
         style="display: none;">
        
        {{-- Header --}}
        <div class="px-4 py-3 border-b border-slate-200">
            <h3 class="text-sm font-semibold text-slate-900">Select AI Model</h3>
            <p class="text-xs text-slate-500 mt-1">Choose your preferred AI model for assistance</p>
        </div>

        {{-- Models by Provider --}}
        <div class="max-h-96 overflow-y-auto">
            @foreach($modelsByProvider as $provider => $models)
                <div class="border-b border-slate-100 last:border-b-0">
                    {{-- Provider Header --}}
                    <div class="px-4 py-2 bg-slate-50 sticky top-0">
                        <p class="text-xs font-semibold uppercase tracking-[0.12em] text-slate-600">{{ $provider }}</p>
                    </div>

                    {{-- Models --}}
                    @foreach($models as $modelId => $model)
                        <button @click="selectModel('{{ $modelId }}')"
                                class="w-full text-left px-4 py-3 hover:bg-blue-50 transition border-b border-slate-100 last:border-b-0"
                                :class="'{{ $currentModel }}' === '{{ $modelId }}' ? 'bg-blue-50' : ''">
                            <div class="flex items-start gap-3">
                                {{-- Icon --}}
                                <div class="flex-shrink-0 mt-0.5">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                                         :class="'{{ $currentModel }}' === '{{ $modelId }}' ? 'bg-blue-100 text-blue-600' : 'bg-slate-100 text-slate-600'">
                                        @switch($model['icon'])
                                            @case('sparkles')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                                </svg>
                                                @break
                                            @case('brain')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5a4 4 0 100-8 4 4 0 000 8z" />
                                                </svg>
                                                @break
                                            @case('zap')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                                </svg>
                                                @break
                                            @case('feather')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                @break
                                            @case('cpu')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2m6-2v2M9 3a2 2 0 00-2 2v0a2 2 0 002 2h0a2 2 0 002-2v0a2 2 0 00-2-2zm0 0h6m-6 0a2 2 0 00-2 2v0a2 2 0 002 2h0a2 2 0 002-2v0a2 2 0 00-2-2zm0 0h6m-6 0a2 2 0 00-2 2v0a2 2 0 002 2h0a2 2 0 002-2v0a2 2 0 00-2-2zm0 0h6m-6 0a2 2 0 00-2 2v0a2 2 0 002 2h0a2 2 0 002-2v0a2 2 0 00-2-2z" />
                                                </svg>
                                                @break
                                            @case('code')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                                                </svg>
                                                @break
                                            @default
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                                </svg>
                                        @endswitch
                                    </div>
                                </div>

                                {{-- Model Info --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <p class="text-sm font-semibold text-slate-900">{{ $model['name'] }}</p>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                                              :class="'{{ $model['tier'] }}' === 'premium' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-700'">
                                            {{ ucfirst($model['tier']) }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-500 mt-0.5">{{ $model['description'] }}</p>
                                </div>

                                {{-- Checkmark --}}
                                @if($currentModel === $modelId)
                                    <div class="flex-shrink-0 mt-0.5">
                                        <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                @endif
                            </div>
                        </button>
                    @endforeach
                </div>
            @endforeach
        </div>

        {{-- Footer --}}
        <div class="px-4 py-3 border-t border-slate-200 bg-slate-50">
            <p class="text-xs text-slate-500">
                <span class="font-semibold">Current:</span> {{ $availableModels[$currentModel]['name'] ?? 'Unknown' }}
            </p>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function aiModelSwitcher() {
        return {
            open: false,
            selectModel(modelId) {
                // Send AJAX request to update model
                fetch('{{ route("api.ai-model.update") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ model_id: modelId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Reload page or update UI
                        window.location.reload();
                    } else {
                        alert('Failed to update AI model');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error updating AI model');
                });
            }
        };
    }
</script>
@endpush
