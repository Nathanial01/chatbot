
#composer require cyrox/chatbot:dev-main#


Cyrox Chatbot
This is a lightweight chatbot package that can be easily embedded into any website via Composer.
It integrates with OpenAI to provide chatbot functionality.
Installation and Setup
1. Install the Package
First, you need to require the package using Composer. Run the following command:
composer require cyrox/chatbot:dev-main
This will install the chatbot package and its dependencies in your project.
2. Configure Service Provider
Once installed, you need to register the ChatbotServiceProvider in your Laravel application. Open
the config/app.php file and add the following line to the providers array:
'providers' => [
// Other Service Providers...
Cyrox\Chatbot\ChatbotServiceProvider::class,
],
This step allows Laravel to recognize and load the chatbot service provider, making the package
functional in your application.
3. Update Composer Configuration
If you manage your composer.json file manually, you should add the following section:
"require": {
"cyrox/chatbot": "dev-main"
},
"repositories": [
{
"type": "vcs",
"url": "https://github.com/Nathanial01/chatbot.git"
}
]
4. Environment Variables
Add your OpenAI API key to your .env file to enable communication with OpenAI services:
OPENAI_API_KEY="your_openai_api_key_here"
5. Frontend Styling
Ensure that your chatbot looks good by including Tailwind CSS in your views. Add the following lines
to the HTML head of your view:
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.3/dist/tailwind.min.css" rel="stylesheet">
<link href="{{ asset('css/style.css') }}" rel="stylesheet">
6. Using the Chatbot Component
Now that everything is set up, you can include the chatbot component in your Laravel blade views
by using:
@include('chatbot::components.chatbot')
This will render the chatbot in your application's frontend.
