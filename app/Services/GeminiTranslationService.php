<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiTranslationService
{
    private string $apiKey;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key') ?? env('GEMINI_API_KEY');
        $this->baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent';
    }

    /**
     * Translate text to target language using Google Gemini API.
     *
     * @param string $text The text to translate
     * @param string $targetLang Target language code (e.g., 'en', 'de')
     * @return array|null Translated content as ['title' => ..., 'content' => ...] or null on failure
     */
    public function translate(string $text, string $targetLang): ?array
    {
        if (empty($this->apiKey)) {
            Log::error('Gemini API key is missing');
            return null;
        }

        $systemPrompt = "You are a compassionate translator for mental healing and spiritual content. Translate the following text to {$targetLang}. Maintain a warm, compassionate, and healing tone. Return the result as JSON with 'title' and 'content' keys. If the text doesn't have an obvious title, create a meaningful one based on the content.";

        $payload = [
            'contents' => [
                [
                    'parts' => [
                        [
                            'text' => $systemPrompt . "\n\nText to translate:\n" . $text
                        ]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.3,
                'topK' => 40,
                'topP' => 0.95,
                'maxOutputTokens' => 2048,
            ]
        ];

        try {
            $response = Http::post($this->baseUrl . '?key=' . $this->apiKey, $payload);

            if (!$response->successful()) {
                Log::error('Gemini API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return null;
            }

            $data = $response->json();
            $content = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';

            // Clean up the response to extract JSON
            $content = trim($content);
            
            // Remove markdown code blocks if present
            if (str_starts_with($content, '```json')) {
                $content = substr($content, 7);
            }
            if (str_ends_with($content, '```')) {
                $content = substr($content, 0, -3);
            }
            $content = trim($content);

            $translated = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Failed to parse Gemini response as JSON', [
                    'content' => $content,
                    'error' => json_last_error_msg()
                ]);
                return null;
            }

            return $translated;

        } catch (\Exception $e) {
            Log::error('Exception during Gemini translation', [
                'error' => $e->getMessage(),
                'text' => substr($text, 0, 100) . '...'
            ]);
            return null;
        }
    }
}
