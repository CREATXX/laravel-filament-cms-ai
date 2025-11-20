<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    {{-- SEO Meta Tags --}}
    <title>@yield('title', App\Models\Setting::get('site_name', 'CMS AI'))</title>
    <meta name="description" content="@yield('meta_description', App\Models\Setting::get('site_description', ''))">
    <meta name="keywords" content="@yield('meta_keywords', '')">
    
    {{-- Open Graph --}}
    <meta property="og:title" content="@yield('og_title', App\Models\Setting::get('site_name'))">
    <meta property="og:description" content="@yield('og_description', App\Models\Setting::get('site_description'))">
    <meta property="og:image" content="@yield('og_image', asset('images/og-image.jpg'))">
    <meta property="og:type" content="@yield('og_type', 'website')">
    
    {{-- Favicon --}}
    <link rel="icon" type="image/png" href="{{ App\Models\Setting::get('site_favicon', asset('favicon.png')) }}">
    
    {{-- Vite Assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @stack('styles')
</head>
<body class="antialiased bg-gray-50">
    {{-- Header --}}
    @include('layouts.partials.header')
    
    {{-- Main Content --}}
    <main class="min-h-screen">
        @yield('content')
    </main>
    
    {{-- Footer --}}
    @include('layouts.partials.footer')
    
    {{-- Scripts --}}
    @stack('scripts')
    
    {{-- Google Analytics --}}
    @if($gaId = App\Models\Setting::get('google_analytics_id'))
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaId }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '{{ $gaId }}');
        </script>
    @endif
</body>
</html>
