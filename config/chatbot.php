<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Chatbot API Key
    |--------------------------------------------------------------------------
    |
    | This is the OpenAI API key used for interacting with the Chatbot. You can
    | set this value in your environment file or directly here.
    |
    */

    'api_key' => env('CHATBOT_API_KEY', 'your-default-api-key'),

    /*
    |--------------------------------------------------------------------------
    | Chatbot Model
    |--------------------------------------------------------------------------
    |
    | The OpenAI model used for generating responses, such as "gpt-3.5-turbo".
    |
    */

    'model' => 'gpt-3.5-turbo',

    /*
    |--------------------------------------------------------------------------
    | Maximum Tokens
    |--------------------------------------------------------------------------
    |
    | The maximum number of tokens to use for Chatbot responses.
    |
    */

    'max_tokens' => 150,

];
