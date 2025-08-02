<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AIController;
use App\Http\Controllers\GeminiAiController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/ai', [AIController::class, 'index']);
Route::post('/ai/chat', [AIController::class, 'chat']);
Route::post('/ai/summarize', [AIController::class, 'summarize']);


Route::get('gemini-ai', [GeminiAiController::class, 'index']);
Route::post('gemini-ai/chat', [GeminiAiController::class, 'chat']);
Route::post('gemini-ai/summarize', [GeminiAiController::class, 'summarize']);
