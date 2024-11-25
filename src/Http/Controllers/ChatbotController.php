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
        $name = $user->name;
        return view('chatbot::chatbot', compact('name'));
    }

    /**
     * Generate a chatbot response and save the conversation in the chat history.
     */
    public function generateResponse(Request $request): JsonResponse
    {
        try {
            $prompt = $request->input('prompt');
            $userId = auth()->check() ? auth()->id() : null;

            // Load conversation context
            $context = ChatHistory::where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->take(5) // Retrieve the last 5 messages
                ->get()
                ->pluck('message')
                ->toArray();

            // Define allowed e-commerce topics
            $allowedKeywords = ['cyroX.org',
                'order', 'track order', 'order status', 'product', 'search product', 'add to cart',
                'cart', 'checkout', 'refund', 'return', 'help', 'support', 'shipping', 'delivery',
                'payment', 'invoice', 'receipt', 'store policies', 'account issues', 'product details',
                'wishlist', 'customer service'
            ];

            // Check if user input contains relevant keywords or part of ongoing flow
            $isRelevant = false;
            $store = 'CyroX'; // Define the store name
            $storeLink = 'https://cyrox.org'; // the store link
            
            // Check if the input contains allowed keywords
            foreach ($allowedKeywords as $keyword) {
                if (str_contains(strtolower($prompt), $keyword)) {
                    $isRelevant = true;
                    break;
                }
            }
            
            // Allow inputs if context exists (ongoing flow)
            if (!$isRelevant && count($context) > 0) {
                $isRelevant = true;
            }
            
            // Check if the user is asking about the store name or link
            if (str_contains(strtolower($prompt), 'store name') || str_contains(strtolower($prompt), 'store link')) {
                $restrictedResponse = "The store name is $store. You can visit it at <a href='$storeLink' target='_blank' style='color:blue;'>$storeLink</a>.";
                ChatHistory::create([
                    'user_id' => $userId,
                    'message' => $prompt,
                    'sender'  => 'user',
                ]);
                ChatHistory::create([
                    'user_id' => $userId,
                    'message' => $restrictedResponse,
                    'sender'  => 'bot',
                ]);
                return response()->json(['response' => $restrictedResponse]);
            }
            
            // If the input is not relevant, return a restricted response
            if (!$isRelevant) {
                $restrictedResponse = "I can only assist with shopping-related tasks like tracking orders, searching for products, or managing your cart.";
                ChatHistory::create([
                    'user_id' => $userId,
                    'message' => $prompt,
                    'sender'  => 'user',
                ]);
                ChatHistory::create([
                    'user_id' => $userId,
                    'message' => $restrictedResponse,
                    'sender'  => 'bot',
                ]);
                return response()->json(['response' => $restrictedResponse]);
            }

            // Handle product search and order status explicitly
            if (str_contains(strtolower($prompt), 'product id') || str_contains(strtolower($prompt), 'order number')) {
                $productId = null;
                $orderNumber = null;

                if (preg_match('/product id\s*:\s*(\d+)/i', $prompt, $matches)) {
                    $productId = $matches[1];
                }

                if (preg_match('/order number\s*:\s*(\d+)/i', $prompt, $matches)) {
                    $orderNumber = $matches[1];
                }

                $result = null;

                if ($productId) {
                    $result = "Product ID $productId corresponds to 'Example Product Name' priced at $50.";
                } elseif ($orderNumber) {
                    $result = "Order number $orderNumber is currently being processed and will be shipped soon.";
                }

                if (!$result) {
                    $result = "Sorry, I couldn't find any details for the given information. Please check and try again.";
                }

                ChatHistory::create([
                    'user_id' => $userId,
                    'message' => $prompt,
                    'sender'  => 'user',
                ]);

                ChatHistory::create([
                    'user_id' => $userId,
                    'message' => $result,
                    'sender'  => 'bot',
                ]);

                return response()->json(['response' => $result]);
            }

            // Handle adding a product to the cart
            if (str_contains(strtolower($prompt), 'add to cart')) {
                $productId = null;
                $productName = null;

                if (preg_match('/product id\s*:\s*(\d+)/i', $prompt, $matches)) {
                    $productId = $matches[1];
                }

                if (preg_match('/product name\s*:\s*([a-zA-Z0-9\s]+)/i', $prompt, $matches)) {
                    $productName = $matches[1];
                }

                if ($productId || $productName) {
                    $cartAction = "The product";
                    if ($productId) {
                        $cartAction .= " with ID $productId";
                    }
                    if ($productName) {
                        $cartAction .= " named '$productName'";
                    }
                    $cartAction .= " has been added to your cart.";

                    ChatHistory::create([
                        'user_id' => $userId,
                        'message' => $prompt,
                        'sender'  => 'user',
                    ]);

                    ChatHistory::create([
                        'user_id' => $userId,
                        'message' => $cartAction,
                        'sender'  => 'bot',
                    ]);

                    return response()->json(['response' => $cartAction]);
                } else {
                    $errorResponse = "Sorry, I couldn't identify the product details. Please provide a valid product name or ID.";
                    ChatHistory::create([
                        'user_id' => $userId,
                        'message' => $prompt,
                        'sender'  => 'user',
                    ]);

                    ChatHistory::create([
                        'user_id' => $userId,
                        'message' => $errorResponse,
                        'sender'  => 'bot',
                    ]);

                    return response()->json(['response' => $errorResponse]);
                }
            }

            // General flow with GPT model
            $systemPrompt = "You are a chatbot for an e-commerce website. Assist users with tasks like tracking orders, searching products, adding items to their cart, and providing store policies. Maintain context for an interactive experience.";

            $messages = [
                ['role' => 'system', 'content' => $systemPrompt]
            ];

            foreach ($context as $message) {
                $messages[] = ['role' => 'assistant', 'content' => $message];
            }

            $messages[] = ['role' => 'user', 'content' => $prompt];

            $response = OpenAI::chat()->create([
                'model' => 'gpt-4-turbo',
                'messages' => $messages,
                'max_tokens' => 500,
            ]);

            $botResponse = $response['choices'][0]['message']['content'];

            ChatHistory::create([
                'user_id' => $userId,
                'message' => $prompt,
                'sender'  => 'user',
            ]);

            ChatHistory::create([
                'user_id' => $userId,
                'message' => $botResponse,
                'sender'  => 'bot',
            ]);

            return response()->json(['response' => $botResponse]);

        } catch (\Exception $e) {
            \Log::error('Chatbot error: ' . $e->getMessage(), [
                'prompt' => $prompt,
                'user_id' => $userId
            ]);

            return response()->json(['error' => 'An internal error occurred. Please try again later.'], 500);
        }
    }
}