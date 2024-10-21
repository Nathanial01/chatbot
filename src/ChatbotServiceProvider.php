<?php

namespace Cyrox\Chatbot;

use Illuminate\Support\ServiceProvider;

class ChatbotServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Load views from the package
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'chatbot');

        // Publish views so users can customize them
        $this->publishes([
            __DIR__.'/../resources/views/chatbot.blade.php' => resource_path('views/vendor/cyrox/chatbot/chatbot.blade.php'),
        ], 'views');

        // Publish the configuration file so users can modify API keys, model, etc.
        $this->publishes([
            __DIR__.'/../config/chatbot.php' => config_path('chatbot.php'),
        ], 'config');

        // Load package routes
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
    }

    public function register()
    {
        // Merge configuration file with the app's config
        $this->mergeConfigFrom(
            __DIR__.'/../config/chatbot.php', 'chatbot'
        );
    }
}
