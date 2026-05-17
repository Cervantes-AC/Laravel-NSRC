<?php

namespace App\Livewire;

use App\Services\ReportService;
use Livewire\Component;

class AIProviderSwitcher extends Component
{
    public string $currentProvider = 'groq';
    public array $providers = ['groq', 'openrouter'];
    public bool $showMessage = false;
    public string $message = '';
    public string $messageType = 'success';

    protected ReportService $reportService;

    public function mount(ReportService $reportService)
    {
        $this->reportService = $reportService;
        $this->currentProvider = session('ai_provider', config('ai.provider', 'groq'));
    }

    public function switchProvider(string $provider)
    {
        try {
            $this->reportService->switchAIProvider($provider);
            $this->currentProvider = $provider;
            $this->message = "Switched to {$provider} provider";
            $this->messageType = 'success';
            $this->showMessage = true;

            $this->dispatch('provider-switched', provider: $provider);
        } catch (\Exception $e) {
            $this->message = 'Error: ' . $e->getMessage();
            $this->messageType = 'error';
            $this->showMessage = true;
        }

        $this->resetMessageAfterDelay();
    }

    public function switchApiKey()
    {
        try {
            $this->reportService->switchAPIKey();
            $this->message = "API key switched for {$this->currentProvider}";
            $this->messageType = 'success';
            $this->showMessage = true;

            $this->dispatch('api-key-switched', provider: $this->currentProvider);
        } catch (\Exception $e) {
            $this->message = 'Error: ' . $e->getMessage();
            $this->messageType = 'error';
            $this->showMessage = true;
        }

        $this->resetMessageAfterDelay();
    }

    private function resetMessageAfterDelay()
    {
        $this->dispatch('reset-message-after-delay');
    }

    public function render()
    {
        return view('livewire.ai-provider-switcher');
    }
}
