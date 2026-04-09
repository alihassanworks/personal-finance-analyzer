<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen items-center justify-center bg-slate-50 px-4 py-12 dark:bg-slate-950">
    <div class="w-full max-w-md">
        <div class="mb-8 text-center">
            <a href="{{ route('home') }}" class="text-xl font-bold text-indigo-600 dark:text-indigo-400">{{ config('app.name') }}</a>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Personal finance analyzer</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            @yield('content')
        </div>
        <p class="mt-6 text-center text-sm text-slate-500 dark:text-slate-400">
            @yield('footer_links')
        </p>
    </div>
</body>
</html>
