<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Laravel Filament CMS AI</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 flex items-center justify-center">
            <div class="max-w-2xl mx-auto text-center">
                <h1 class="text-4xl font-bold text-gray-900 mb-4">
                    🚀 Laravel Filament CMS AI
                </h1>
                <p class="text-lg text-gray-600 mb-8">
                    Laravel 11 + Filament v3 + OpenAI ile güçlendirilmiş dinamik CMS sistemi
                </p>
                <div class="space-x-4">
                    <a href="/admin" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">
                        Admin Paneli
                    </a>
                    <a href="https://github.com/CREATXX/laravel-filament-cms-ai" class="inline-block bg-gray-800 text-white px-6 py-3 rounded-lg hover:bg-gray-900">
                        GitHub
                    </a>
                </div>
            </div>
        </div>
    </body>
</html>
