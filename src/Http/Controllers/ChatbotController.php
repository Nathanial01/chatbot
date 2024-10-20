<?php
namespace Cyrox\Chatbot\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use App\Models\User;
use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;
use Cyrox\Chatbot\Models\ChatHistory;

class ChatbotController extends BaseController
{
    public function index(User $user)
    {
        $name = $user->name;
        return view('components.chatbot', compact('name'));
    }

    public function generateResponse(Request $request): JsonResponse
    {
        try {
            $prompt = $request->input('prompt');
            $userId = auth()->check() ? auth()->id() : null;

            // Save user's message
            ChatHistory::create([
                'user_id' => $userId,
                'message' => $prompt,
                'sender'  => 'user',
            ]);

            // Call OpenAI API for response using config values
            $response = OpenAI::chat()->create([
                'model' => config('Chatbot.model'), // Get model from configuration
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a helpful assistant.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'max_tokens' => config('Chatbot.max_tokens'), // Get max_tokens from configuration
            ]);

            $botResponse = $response['choices'][0]['message']['content'];

            // Save bot response
            ChatHistory::create([
                'user_id' => $userId,
                'message' => $botResponse,
                'sender'  => 'bot',
            ]);

            return response()->json(['response' => $botResponse]);
        } catch (\Exception $e) {
            // Log error with more context
            \Log::error('Chatbot error: ' . $e->getMessage(), [
                'prompt' => $request->input('prompt'),
                'user_id' => $userId
            ]);

            return response()->json(['error' => 'An internal error occurred. Please try again later.'], 500);
        }
    }
}
