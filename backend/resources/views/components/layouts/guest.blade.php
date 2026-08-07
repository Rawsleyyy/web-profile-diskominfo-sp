<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Login Admin' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-slate-50">
    <main class="relative flex min-h-screen items-center justify-center overflow-hidden px-4 py-10">
        <div class="absolute inset-0 -z-10 bg-gradient-to-br from-blue-50 via-white to-red-50/40"></div>
        <div class="absolute -left-28 -top-28 -z-10 h-80 w-80 rounded-full bg-blue-200/30 blur-3xl"></div>
        <div class="absolute -bottom-28 -right-28 -z-10 h-80 w-80 rounded-full bg-red-200/20 blur-3xl"></div>
        {{ $slot }}
    </main>

    @livewireScripts
</body>
</html>
