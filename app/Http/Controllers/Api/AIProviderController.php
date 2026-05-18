<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AIProviderController extends Controller
{
    public function switchProvider(Request $request, ReportService $reportService): JsonResponse
    {
        $provider = $request->input('provider', 'groq');
        try {
            $reportService->switchAIProvider($provider);
            return response()->json(['success' => true, 'message' => "Switched to {$provider} provider", 'provider' => $provider]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function switchApiKey(Request $request, ReportService $reportService): JsonResponse
    {
        try {
            $reportService->switchAPIKey();
            return response()->json(['success' => true, 'message' => 'API key switched successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}
