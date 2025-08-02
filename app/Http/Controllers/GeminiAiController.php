<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GeminiService;

class GeminiAiController extends Controller
{
    protected $gemini;

    public function __construct(GeminiService $gemini)
    {
        $this->gemini = $gemini;
    }

    public function chat(Request $request)
    {
        $message = $request->input('message');
        $response = $this->gemini->chatWithGemini($message);
        return response()->json(['response' => $response]);
    }

    public function summarize(Request $request)
    {
        $text = $request->input('text');
        $summary = $this->gemini->summarizeText($text);
        return response()->json(['summary' => $summary]);
    }

    public function index()
    {
        return view('gemini.index');
    }
}
