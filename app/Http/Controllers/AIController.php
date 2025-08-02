<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\OpenAIService;

class AIController extends Controller
{
    protected $openAI;

    public function __construct(OpenAIService $openAI)
    {
        $this->openAI = $openAI;
    }

    public function index()
    {
        return view('ai.index');
    }


    public function chat(Request $request)
    {
        $userInput = $request->input('message');

        $messages = [
            ['role' => 'system', 'content' => 'You are a helpful assistant.'],
            ['role' => 'user', 'content' => $userInput]
        ];

        $response = $this->openAI->chatWithGPT($messages);

        return response()->json(['response' => $response]);
    }

    public function summarize(Request $request)
    {
        $text = $request->input('text');

        $summary = $this->openAI->summarizeText($text);

        return response()->json(['summary' => $summary]);
    }
}
