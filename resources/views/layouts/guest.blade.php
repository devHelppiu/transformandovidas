<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Transformando Vidas') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=urbanist:400,500,600,700,800|montserrat:400,500,600,700|fira-sans:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-urbanist text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-tv-bg">
            {{-- Decorative blobs --}}
            <div class="fixed -top-32 -right-32 w-96 h-96 rounded-full bg-tv-pink/10 blur-3xl pointer-events-none"></div>
            <div class="fixed -bottom-24 -left-24 w-80 h-80 rounded-full bg-tv-blue/10 blur-3xl pointer-events-none"></div>

            <div class="relative z-10 mb-8 py-2">
                <a href="/" class="flex items-center justify-center">
                    <img src="{{ asset('images/logo.svg') }}" alt="Transformando Vidas" class="h-24 w-auto block">
                </a>
            </div>

            <div class="relative z-10 w-full sm:max-w-md px-8 py-8 bg-white shadow-xl shadow-tv-blue/5 overflow-hidden rounded-2xl border border-gray-100">
                {{ $slot }}
            </div>

            {{-- Link volver al inicio --}}
            <a href="/" class="relative z-10 mt-6 text-sm text-gray-500 hover:text-tv-blue transition-colors">
                ← Volver al inicio
            </a>
        </div>
    </body>
</html>
