<?php

namespace App\Livewire;

use App\Services\AIService;
use Livewire\Component;

class Chatbot extends Component
{
    public string $message = '';

    public array $conversation = [];

    public string $mode = 'agent';

    public bool $loading = false;

    public function mount(): void
    {
        $user = auth()->user();
        if ($user && !$user->isAdmin()) {
            $this->mode = 'agent';
        }
    }

    public function sendMessage(AIService $aiService): void
    {
        if (empty(trim($this->message))) {
            return;
        }

        $this->loading = true;

        $userMessage = $this->message;
        $this->conversation[] = [
            'role' => 'user',
            'content' => $userMessage,
            'timestamp' => now()->toIso8601String(),
        ];

        $this->message = '';

        $response = $aiService->processQuery($userMessage, auth()->user());

        $this->conversation[] = [
            'role' => 'assistant',
            'content' => $response,
            'timestamp' => now()->toIso8601String(),
        ];

        $this->loading = false;
    }

    public function switchMode(string $mode, AIService $aiService): void
    {
        $user = auth()->user();

        if (!$user || !$user->isAdmin()) {
            $this->mode = 'agent';
            return;
        }

        $available = $aiService->getAvailableModes($user);

        if (in_array($mode, $available, true)) {
            $this->mode = $mode;
        }
    }

    public function clearConversation(): void
    {
        $this->conversation = [];
        $this->message = '';
    }

    public function render()
    {
        return view('livewire.chatbot')
            ->layout('components.layouts.app');
    }
}
