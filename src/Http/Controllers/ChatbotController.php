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
    /**
     * Display the chatbot interface.
     */
    public function index(User $user)
    {
        // Get the user's name to personalize the chatbot experience
        $name = $user->name;
        return view('chatbot::chatbot', compact('name'));
    }

    /**
     * Generate a chatbot response and save the conversation in the chat history.
     */
    public function generateResponse(Request $request): JsonResponse
    {
        try {
            // Get the prompt from the request
            $prompt = $request->input('prompt');
            // Get the authenticated user ID if available
            $userId = auth()->check() ? auth()->id() : null;

            // Save the user's message in the chat history
            ChatHistory::create([
                'user_id' => $userId,
                'message' => $prompt,
                'sender'  => 'user',
            ]);

            // Call the OpenAI API to generate a response
            $response = OpenAI::chat()->create([
                'model' => config('chatbot.model'), // Model from configuration
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a helpful assistant.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'max_tokens' => config('chatbot.max_tokens'), // Max tokens from configuration
            ]);

            // Extract the bot's response from the OpenAI API response
            $botResponse = $response['choices'][0]['message']['content'];

            // Save the bot's response in the chat history
            ChatHistory::create([
                'user_id' => $userId,
                'message' => $botResponse,
                'sender'  => 'bot',
            ]);

            // Return the bot's response as a JSON response
            return response()->json(['response' => $botResponse]);

        } catch (\Exception $e) {
            // Log the error for debugging purposes
            \Log::error('Chatbot error: ' . $e->getMessage(), [
                'prompt' => $prompt,
                'user_id' => $userId
            ]);

            // Return an error message in case of failure
            return response()->json(['error' => 'An internal error occurred. Please try again later.'], 500);
        }
    }
}
