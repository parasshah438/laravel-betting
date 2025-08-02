<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class OpenAIService
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.openai.key');
        $this->baseUrl = 'https://api.openai.com/v1';
    }

    public function chatWithGPT($messages, $model = 'gpt-3.5-turbo')
    {
        $response = Http::withOptions([
                'verify' => public_path('cacert.pem'),
            ])
            ->withToken($this->apiKey)
            ->post($this->baseUrl . '/chat/completions', [
                'model' => $model,
                'messages' => $messages,
                'temperature' => 0.7,
            ]);
            \Log::info('OpenAI raw response', $response->json());

        return $response->json('choices.0.message.content');
    }

    public function summarizeText($inputText)
    {
        $messages = [
            ['role' => 'system', 'content' => 'You are a helpful assistant that summarizes text.'],
            ['role' => 'user', 'content' => "Summarize this:\n\n" . $inputText]
        ];

        return $this->chatWithGPT($messages);
    }
}
