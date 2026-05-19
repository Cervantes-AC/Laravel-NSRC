<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('AI ChatBot') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div x-data="chatbotApp()" class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-200">
                <!-- Header -->
                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-6 text-white">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-white/20 backdrop-blur flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold">AI Assistant</h1>
                            <p class="text-white/80 text-sm">Powered by {{ ucfirst(config('ai.provider', 'Groq')) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Chat Container -->
                <div class="flex flex-col h-[600px]">
                    <!-- Messages Area -->
                    <div class="flex-1 overflow-y-auto p-6 space-y-4 bg-gray-50" id="messagesContainer">
                        <!-- Empty State -->
                        <div x-show="messages.length === 0" class="flex justify-center">
                            <div class="text-center text-gray-500">
                                <svg class="w-12 h-12 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                                <p class="text-sm">Start a conversation with the AI assistant</p>
                            </div>
                        </div>

                        <!-- Messages List -->
                        <template x-for="(message, index) in messages" :key="index">
                            <div>
                                <!-- User Message -->
                                <div x-show="message.role === 'user'" class="flex justify-end mb-4">
                                    <div class="max-w-[75%] bg-blue-500 text-white rounded-2xl rounded-br-sm px-4 py-3 shadow-sm">
                                        <p class="text-sm whitespace-pre-wrap" x-text="message.content"></p>
                                    </div>
                                </div>

                                <!-- Assistant Message -->
                                <div x-show="message.role === 'assistant'" class="flex justify-start mb-4">
                                    <div class="max-w-[75%] bg-white border border-gray-200 rounded-2xl rounded-bl-sm px-4 py-3 shadow-sm">
                                        <p class="text-sm whitespace-pre-wrap text-gray-800" x-text="message.content"></p>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Input Area -->
                    <div class="border-t border-gray-200 p-6 bg-white">
                        <form @submit.prevent="sendMessage" class="space-y-4">
                            <div class="flex gap-3">
                                <input
                                    type="text"
                                    x-model="currentMessage"
                                    placeholder="Type your message..."
                                    class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    :disabled="isLoading"
                                />
                                <button
                                    type="submit"
                                    :disabled="isLoading || !currentMessage.trim()"
                                    class="px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 disabled:bg-gray-400 disabled:cursor-not-allowed transition font-medium flex items-center gap-2"
                                >
                                    <span x-show="!isLoading">Send</span>
                                    <span x-show="isLoading" class="flex items-center gap-2">
                                        <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Processing...
                                    </span>
                                </button>
                            </div>

                            <!-- Error Message -->
                            <div x-show="error" class="p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm" x-text="error"></div>

                            <!-- Info -->
                            <div class="text-xs text-gray-500 text-center">
                                Powered by AI • Responses are generated in real-time
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
                <button @click="quickAction('What is the attendance policy?')" class="p-4 bg-white border border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition text-left">
                    <p class="font-semibold text-gray-900 text-sm">Attendance Policy</p>
                    <p class="text-gray-600 text-xs mt-1">Learn about attendance rules</p>
                </button>
                <button @click="quickAction('How do I log my time?')" class="p-4 bg-white border border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition text-left">
                    <p class="font-semibold text-gray-900 text-sm">Time Logging</p>
                    <p class="text-gray-600 text-xs mt-1">Get help with time tracking</p>
                </button>
                <button @click="quickAction('What are the system features?')" class="p-4 bg-white border border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition text-left">
                    <p class="font-semibold text-gray-900 text-sm">System Features</p>
                    <p class="text-gray-600 text-xs mt-1">Explore available features</p>
                </button>
            </div>
        </div>
    </div>

    <script>
        function chatbotApp() {
            return {
                currentMessage: '',
                messages: [],
                isLoading: false,
                error: '',

                async sendMessage() {
                    if (!this.currentMessage.trim()) return;

                    this.error = '';
                    const userMessage = this.currentMessage;
                    this.currentMessage = '';

                    // Add user message to display
                    this.messages.push({
                        role: 'user',
                        content: userMessage,
                        timestamp: new Date()
                    });

                    this.isLoading = true;
                    this.scrollToBottom();

                    try {
                        const response = await fetch('{{ route("api.chatbot.send") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                message: userMessage,
                                conversation_history: this.messages.slice(0, -1).map(m => ({
                                    role: m.role,
                                    content: m.content
                                }))
                            })
                        });

                        const data = await response.json();

                        if (data.success) {
                            this.messages.push({
                                role: 'assistant',
                                content: data.message,
                                timestamp: new Date()
                            });
                        } else {
                            this.error = data.message || 'Failed to get response';
                            this.messages.pop(); // Remove user message on error
                        }
                    } catch (err) {
                        this.error = 'Network error. Please try again.';
                        this.messages.pop(); // Remove user message on error
                    } finally {
                        this.isLoading = false;
                        this.$nextTick(() => this.scrollToBottom());
                    }
                },

                quickAction(message) {
                    this.currentMessage = message;
                    this.sendMessage();
                },

                scrollToBottom() {
                    this.$nextTick(() => {
                        const container = document.getElementById('messagesContainer');
                        if (container) {
                            container.scrollTop = container.scrollHeight;
                        }
                    });
                }
            };
        }
    </script>

    <style>
        [x-cloak] { display: none; }
    </style>
</x-app-layout>
