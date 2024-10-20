<?php

namespace Cyrox\Chatbot;

use Illuminate\Support\ServiceProvider;

class ChatbotServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Load all views from the package
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'Chatbot-package');

        // Publish views to allow users to customize them
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/Chatbot-package'),
        ], 'views');

        // Publish configuration file to the config directory
        $this->publishes([
            __DIR__.'/../config/chatbot.php' => config_path('chatbot.php'),
        ], 'config');
    }

    public function register()
    {
        // Merge the package configuration with the user's config
        $this->mergeConfigFrom(
            __DIR__.'/../config/chatbot.php', 'chatbot'
        );
    }
}
