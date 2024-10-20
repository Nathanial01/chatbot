<?php

use Illuminate\Support\Facades\Route;
use Cyrox\Chatbot\Http\Controllers\ChatbotController; // Ensure correct namespace

Route::group(['prefix' => 'chatbot', 'as' => 'chatbot.'], function () {
    Route::get('/', [ChatbotController::class, 'index'])->name('index');  // Renders the Chatbot page
    Route::post('/generate-response', [ChatbotController::class, 'generateResponse'])->name('generate'); // Handles Chatbot response
});
