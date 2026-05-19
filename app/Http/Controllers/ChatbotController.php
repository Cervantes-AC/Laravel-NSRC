<?php

namespace App\Http\Controllers;

use App\Services\ChatBotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatBotController extends Controller
{
    private ChatBotService $chatBotService;

    public function __construct(ChatBotService $chatBotService)
    {
        $this->chatBotService = $chatBotService;
    }

    /**
     * Display the chatbot page
     */
    public function index()
    {
        return view('chatbot.index');
    }

    /**
     * Send a message to the chatbot
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'conversation_history' => 'nullable|array',
        ]);

        $response = $this->chatBotService->chat(
            $validated['message'],
            $validated['conversation_history'] ?? []
        );

        return response()->json($response);
    }

    /**
     * Get available AI models
     */
    public function getModels(): JsonResponse
    {
        $models = $this->chatBotService->getAvailableModels();

        return response()->json([
            'success' => true,
            'models' => $models,
        ]);
    }

    /**
     * Stream chat response (for real-time updates)
     */
    public function streamMessage(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'conversation_history' => 'nullable|array',
        ]);

        return response()->stream(function () use ($validated) {
            $response = $this->chatBotService->chat(
                $validated['message'],
                $validated['conversation_history'] ?? []
            );

            echo json_encode($response);
        }, 200, [
            'Content-Type' => 'application/json',
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
        ]);
    }
}
