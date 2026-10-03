<?php

namespace App\Http\Controllers;

use App\Services\GeminiService;
use Illuminate\Http\Request;

class AiChatController extends Controller
{
    public function chat(Request $request, GeminiService $gemini)
    {
        $data = $request->validate([
            'message' => 'required|string|max:2000',
            'history' => 'nullable|array|max:20',
            'history.*.role' => 'nullable|in:user,model',
            'history.*.text' => 'nullable|string|max:2000',
            'module' => 'nullable|string|max:100',
        ]);

        $reply = $gemini->tutorChat(
            $data['message'],
            $data['history'] ?? [],
            $data['module'] ?? null
        );

        return response()->json(['reply' => $reply]);
    }
}
