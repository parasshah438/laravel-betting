<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GeminiService
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key');
        $this->baseUrl = 'https://generativelanguage.googleapis.com/v1';
    }

    public function chatWithGemini($prompt, $model = 'gemini-2.5-flash')
    {   
        $url = "{$this->baseUrl}/models/{$model}:generateContent?key={$this->apiKey}";

        $response = Http::withOptions([
                'verify' => public_path('cacert.pem'), // only if needed on localhost
            ])
            ->post($url, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ]
            ]);
           \Log::debug('Raw Gemini response body: ' . $response->body());


        $data = $response->json();

        return $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
    }

    public function summarizeText($text)
    {
        $prompt = "Summarize this text:\n\n" . $text;
        return $this->chatWithGemini($prompt);
    }
}
