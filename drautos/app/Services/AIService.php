<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIService
{
    /**
     * Summarize activity logs into catchy news headlines using Google Gemini.
     */
    public static function summarizeActivities($activities)
    {
        $apiKey = env('GEMINI_API_KEY');
        
        // Return null if no API key is configured; dashboard will fall back to manual logs
        if (!$apiKey || $activities->isEmpty()) {
            return null;
        }

        $logText = "";
        foreach ($activities as $log) {
            $logText .= "- " . $log->action . ": " . strip_tags($log->description) . " (by " . ($log->user->name ?? 'System') . ")\n";
        }

        try {
            $prompt = "You are a professional business news editor for 'Danyal Autos'. 
                      Below are the raw activity logs from the last 24 hours. 
                      Write 3 to 5 very short, catchy, and professional news headlines (max 15 words each). 
                      Focus on sales success, inventory updates, and staff productivity. 
                      Format them as a single line separated by ' • '. 
                      Do not use markdown, bold text, or symbols other than the separator.
                      
                      Logs:
                      " . $logText;

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'topK' => 40,
                    'topP' => 0.95,
                    'maxOutputTokens' => 200,
                ]
            ]);

            if ($response->successful()) {
                $result = $response->json();
                if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                    return trim($result['candidates'][0]['content']['parts'][0]['text']);
                }
            } else {
                Log::warning("Gemini API Error: " . $response->body());
            }
        } catch (\Exception $e) {
            Log::error("AI Summarization Exception: " . $e->getMessage());
        }

        return null;
    }
}
