<?php

namespace App\Services;

use App\Models\ConversationHistory;
use App\Models\DutySession;
use App\Models\User;
use App\Models\UserPreference;
use App\Models\VolunteerMetrics;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIService
{
    private const MODES = ['supertool', 'agent', 'teammate'];

    public function sendMessage(string $message, array $context = []): string
    {
        $apiKey = config('attendance.ai_api_key') ?? config('services.groq.api_key') ?? env('GROQ_API_KEY');
        $apiUrl = config('attendance.ai_api_url') ?? 'https://api.groq.com/openai/v1/chat/completions';
        $model = config('attendance.ai_model') ?? 'llama-3.3-70b-versatile';

        $systemContext = $this->getSystemContext();
        $context = array_merge($systemContext, $context);

        $payload = [
            'model' => $model,
            'messages' => [
                ['role' => 'system', 'content' => $this->buildSystemPrompt($context)],
                ['role' => 'user', 'content' => $message],
            ],
            'temperature' => 0.7,
            'max_tokens' => 2048,
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(30)->post($apiUrl, $payload);

            if ($response->failed()) {
                Log::error('AI API request failed', ['status' => $response->status(), 'body' => $response->body()]);
                return 'I encountered an error processing your request. Please try again later.';
            }

            $data = $response->json();

            return $data['choices'][0]['message']['content'] ?? 'No response generated.';
        } catch (\Exception $e) {
            Log::error('AI service exception: ' . $e->getMessage());
            return 'Sorry, I am currently unavailable. Please try again later.';
        }
    }

    public function getSystemContext(): array
    {
        $totalVolunteers = User::count();
        $activeSessions = DutySession::whereNull('time_out')->count();
        $todaySessions = DutySession::whereDate('created_at', today())->count();

        return [
            'total_volunteers' => $totalVolunteers,
            'active_sessions' => $activeSessions,
            'today_sessions' => $todaySessions,
            'timestamp' => now()->toDateTimeString(),
        ];
    }

    public function processQuery(string $query, User $user): string
    {
        $context = [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'role' => $user->role,
            ],
            'user_metrics' => $this->getUserMetrics($user),
        ];

        $response = $this->sendMessage($query, $context);

        ConversationHistory::create([
            'user_id' => $user->id,
            'message' => $query,
            'response' => $response,
            'mode' => 'agent',
        ]);

        return $response;
    }

    public function getAvailableModes(User $user): array
    {
        if ($user->role === 'admin') {
            return self::MODES;
        }

        return ['agent'];
    }

    private function buildSystemPrompt(array $context): string
    {
        return "You are an AI assistant for the NSRC Attendance Management System. "
            . "Current system context: " . json_encode($context) . " "
            . "Provide helpful, concise responses about attendance data, volunteer metrics, and system operations.";
    }

    private function getUserMetrics(User $user): array
    {
        $metrics = VolunteerMetrics::where('volunteer_id', $user->id)->first();
        $sessions = DutySession::where('volunteer_id', $user->id)->get();

        return [
            'total_sessions' => $sessions->count(),
            'total_minutes' => $sessions->sum('duration_minutes'),
            'metrics' => $metrics ? $metrics->toArray() : null,
        ];
    }
}
