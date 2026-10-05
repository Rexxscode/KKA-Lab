<?php

namespace App\Http\Controllers;

use App\Models\AiChat;
use App\Services\GeminiService;
use Illuminate\Http\Request;

class AiChatController extends Controller
{
    public const KUOTA_HARIAN = 30;

    public function chat(Request $request, GeminiService $gemini)
    {
        $data = $request->validate([
            'message' => 'required|string|max:2000',
            'history' => 'nullable|array|max:20',
            'history.*.role' => 'nullable|in:user,model',
            'history.*.text' => 'nullable|string|max:2000',
            'module' => 'nullable|string|exists:modules,slug',
        ]);

        $user = $request->user();

        if (AiChat::where('user_id', $user->id)->whereDate('created_at', today())->count() >= self::KUOTA_HARIAN) {
            return response()->json(['message' => 'Kuota AI harian habis (30/hari). Coba lagi besok ya!'], 429);
        }

        $reply = $gemini->tutorChat(
            $data['message'],
            $data['history'] ?? [],
            $data['module'] ?? null
        );

        AiChat::create([
            'user_id' => $user->id,
            'module_slug' => $data['module'] ?? null,
            'message' => $data['message'],
            'reply' => $reply,
        ]);

        return response()->json(['reply' => $reply]);
    }
}
