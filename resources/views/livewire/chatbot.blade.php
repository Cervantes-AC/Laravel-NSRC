<div class="flex flex-col h-full" aria-label="{{ __('Chatbot') }}">
    <div class="flex items-center justify-between p-4 border-b border-gray-200 bg-gray-50 rounded-t-lg">
        <h3 class="text-sm font-medium text-gray-900">{{ __('Assistant') }}</h3>
        @if(auth()->user() && auth()->user()->role === 'admin')
            <div>
                <label for="mode-selector" class="sr-only">{{ __('Chat mode') }}</label>
                <select id="mode-selector" wire:model="mode" class="text-xs rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" aria-label="{{ __('Chat mode') }}">
                    <option value="general">{{ __('General') }}</option>
                    <option value="reports">{{ __('Reports') }}</option>
                    <option value="analytics">{{ __('Analytics') }}</option>
                    <option value="support">{{ __('Support') }}</option>
                </select>
            </div>
        @endif
    </div>

    <div class="flex-1 overflow-y-auto p-4 space-y-3 min-h-0" wire:poll.5s>
        @if($loading)
            <div class="flex items-center justify-center py-8" role="status">
                <svg class="animate-spin h-6 w-6 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="ml-2 text-sm text-gray-500">{{ __('Thinking...') }}</span>
            </div>
        @endif

        @forelse($messages ?? [] as $msg)
            <div class="flex {{ $msg['role'] === 'user' ? 'justify-end' : 'justify-start' }}">
                <div class="max-w-xs lg:max-w-md px-4 py-2 rounded-lg text-sm {{ $msg['role'] === 'user' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-900' }}">
                    {{ $msg['content'] }}
                </div>
            </div>
        @empty
            <p class="text-center text-sm text-gray-400 py-8">{{ __('How can I help you today?') }}</p>
        @endforelse
    </div>

    <div class="flex flex-wrap gap-2 px-4 py-2 border-t border-gray-200 bg-gray-50">
        <button wire:click="quickAction('{{ __('How do I log attendance?') }}')" class="px-3 py-1 text-xs bg-white border border-gray-200 rounded-full hover:bg-gray-100 transition" aria-label="{{ __('Quick action: How do I log attendance?') }}">
            {{ __('Log attendance?') }}
        </button>
        <button wire:click="quickAction('{{ __('Show my stats') }}')" class="px-3 py-1 text-xs bg-white border border-gray-200 rounded-full hover:bg-gray-100 transition" aria-label="{{ __('Quick action: Show my stats') }}">
            {{ __('My stats') }}
        </button>
        <button wire:click="quickAction('{{ __('Generate report') }}')" class="px-3 py-1 text-xs bg-white border border-gray-200 rounded-full hover:bg-gray-100 transition" aria-label="{{ __('Quick action: Generate report') }}">
            {{ __('Generate report') }}
        </button>
    </div>

    <div class="p-4 border-t border-gray-200">
        <form wire:submit.prevent="sendMessage" class="flex gap-2">
            <label for="chat-input" class="sr-only">{{ __('Type your message') }}</label>
            <input id="chat-input" wire:model="message" type="text" placeholder="{{ __('Type your message...') }}" class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" aria-label="{{ __('Chat message input') }}" />
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition" aria-label="{{ __('Send message') }}">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                </svg>
            </button>
        </form>
    </div>
</div>
