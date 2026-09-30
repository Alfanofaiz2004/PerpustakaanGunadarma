<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Perpustakaan Universitas Gunadarma') }}</title>

        <!-- Font Open Sans -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            [x-cloak] { display: none !important; }
            body, html { font-family: 'Open Sans', sans-serif; }
            @keyframes pageFadeIn {
                from { opacity: 0; transform: translateY(6px); }
                to { opacity: 1; transform: translateY(0); }
            }
            .animate-page-entry {
                animation: pageFadeIn 0.25s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            }
        </style>
    </head>
    <body class="bg-slate-100/90 bg-[radial-gradient(#cbd5e1_1.2px,transparent_1.2px)] [background-size:24px_24px] text-slate-800 antialiased min-h-screen flex flex-col font-sans selection:bg-sky-100 selection:text-sky-900">
        @include('layouts.navigation')

        @if (isset($header))
            <header class="bg-white/80 backdrop-blur-md border-b border-slate-200 shadow-xs">
                <div class="w-full max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <main class="flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6 animate-page-entry">
            <x-alert />
            @yield('content')
            {{ $slot ?? '' }}
        </main>

        <footer class="bg-white/90 backdrop-blur-md border-t border-slate-200 mt-auto">
            <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5 flex flex-col md:flex-row justify-between items-center gap-3 text-xs text-slate-500">
                <p>&copy; {{ date('Y') }} Perpustakaan Universitas Gunadarma. Hak Cipta Dilindungi.</p>
                <div class="flex items-center gap-4 text-[11px]">
                    <span class="text-slate-400">Teknik Informatika &amp; Sistem Informasi</span>
                    <span>•</span>
                    <span class="text-slate-400">Kampus Depok, Kalimalang, Cengkareng &amp; Karawaci</span>
                </div>
            </div>
        </footer>
    </body>
</html>
