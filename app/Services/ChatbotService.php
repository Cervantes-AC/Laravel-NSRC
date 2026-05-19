<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatBotService
{
    private string $apiKey;
    private string $provider;
    private string $model;
    private string $endpoint;

    public function __construct()
    {
        $this->provider = config('ai.provider', 'groq');
        
        if ($this->provider === 'groq') {
            $this->apiKey = config('ai.groq.api_key_1', '');
            $this->model = config('ai.groq.model', 'llama-3.3-70b-versatile');
            $this->endpoint = config('ai.groq.endpoint', 'https://api.groq.com/openai/v1/chat/completions');
        } else {
            $this->apiKey = config('ai.openrouter.api_key_1', '');
            $this->model = config('ai.openrouter.model', 'mistralai/mistral-7b-instruct');
            $this->endpoint = config('ai.openrouter.endpoint', 'https://openrouter.ai/api/v1/chat/completions');
        }
    }

    /**
     * Send a message to the AI and get a response
     */
    public function chat(string $message, array $conversationHistory = []): array
    {
        try {
            if ($this->provider === 'groq' || $this->provider === 'openrouter') {
                return $this->chatWithOpenAICompatible($message, $conversationHistory);
            }

            return [
                'success' => false,
                'message' => 'Unsupported AI provider',
                'error' => 'Provider not configured',
            ];
        } catch (\Exception $e) {
            Log::error('ChatBot Error: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'An error occurred while processing your request',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Chat with OpenAI-compatible API (Groq, OpenRouter, etc.)
     */
    private function chatWithOpenAICompatible(string $message, array $conversationHistory): array
    {
        if (! $this->apiKey) {
            return [
                'success' => false,
                'message' => ucfirst($this->provider).' API key not configured',
                'error' => 'Missing API key',
            ];
        }

        // Build messages array
        $messages = [];

        // Add system message
        $messages[] = [
            'role' => 'system',
            'content' => 'You are a helpful assistant for the NSRC AMS (Attendance Management System). Provide concise and helpful responses. Be friendly and professional.',
        ];

        // Add conversation history
        foreach ($conversationHistory as $item) {
            $messages[] = [
                'role' => $item['role'],
                'content' => $item['content'],
            ];
        }

        // Add current message
        $messages[] = [
            'role' => 'user',
            'content' => $message,
        ];

        try {
            $headers = [
                'Authorization' => 'Bearer '.$this->apiKey,
                'Content-Type' => 'application/json',
            ];

            // Add OpenRouter specific header if needed
            if ($this->provider === 'openrouter') {
                $headers['HTTP-Referer'] = config('app.url', 'http://localhost');
            }

            $response = Http::withHeaders($headers)
                ->timeout(60)
                ->retry(2, 500)
                ->post($this->endpoint, [
                'model' => $this->model,
                'messages' => $messages,
                'temperature' => config('ai.temperature', 0.7),
                'max_tokens' => config('ai.max_tokens', 1024),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $reply = $data['choices'][0]['message']['content'] ?? 'No response received';

                return [
                    'success' => true,
                    'message' => $reply,
                    'tokens_used' => $data['usage']['total_tokens'] ?? 0,
                ];
            }

            $errorMessage = $response->json()['error']['message'] ?? 'Unknown error';
            Log::error('AI API Error: '.$errorMessage, ['response' => $response->json()]);

            return [
                'success' => false,
                'message' => 'Failed to get response from '.ucfirst($this->provider),
                'error' => $errorMessage,
            ];
        } catch (\Exception $e) {
            Log::error(ucfirst($this->provider).' API Error: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Error communicating with '.ucfirst($this->provider),
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get available models
     */
    public function getAvailableModels(): array
    {
        return [
            'groq' => [
                'llama-3.3-70b-versatile' => 'Llama 3.3 70B (Fast & Powerful)',
                'llama-3.1-70b-versatile' => 'Llama 3.1 70B',
                'mixtral-8x7b-32768' => 'Mixtral 8x7B',
            ],
            'openrouter' => [
                'mistralai/mistral-7b-instruct' => 'Mistral 7B',
                'meta-llama/llama-2-70b-chat' => 'Llama 2 70B',
                'openai/gpt-3.5-turbo' => 'GPT-3.5 Turbo',
            ],
        ];
    }
}
