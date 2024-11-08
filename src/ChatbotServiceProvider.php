<?php

namespace Cyrox\Chatbot;

use Illuminate\Support\ServiceProvider;

class ChatbotServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations/CreateChatHistoriesTable');

        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'CreateChatHistoriesTable');


        $this->publishes([
            __DIR__.'/../public' => public_path('vendor/cyrox/chatbot'),
        ], 'cyrox-chatbot-assets');
        $this->publishes([
            __DIR__.'/../public' => public_path('vendor/cyrox/chatbot'),
        ], 'public');
        // Load views from the package
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'chatbot');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'chatbot.generate');
        // Publish views
        $this->publishes([
            __DIR__.'/../resources/views/chatbot.blade.php' => resource_path('views/vendor/cyrox/chatbot/chatbot.blade.php'),
        ], 'views');


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
