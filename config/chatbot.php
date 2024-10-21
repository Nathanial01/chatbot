<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Chatbot API Key
    |--------------------------------------------------------------------------
    |
    | This is the OpenAI API key used for interacting with the Chatbot. You can
    | set this value in your .env file as 'CHATBOT_API_KEY'. If no value is
    | provided, a default or fallback key will be used.
    |
    */

    'api_key' => env('CHATBOT_API_KEY', 'your-default-api-key'),

    /*
    |--------------------------------------------------------------------------
    | Chatbot Model
    |--------------------------------------------------------------------------
    |
    | The OpenAI model used for generating responses. You can set this value
    | in your .env file as 'CHATBOT_MODEL'. The default value is 'gpt-3.5-turbo'.
    |
    */

    'model' => env('CHATBOT_MODEL', 'gpt-3.5-turbo'),

    /*
    |--------------------------------------------------------------------------
    | Maximum Tokens
    |--------------------------------------------------------------------------
    |
    | The maximum number of tokens to use for Chatbot responses. You can also
    | set this value in the .env file as 'CHATBOT_MAX_TOKENS'. If not set, the
    | default value of 150 will be used.
    |
    */

    'max_tokens' => env('CHATBOT_MAX_TOKENS', 150),

];
