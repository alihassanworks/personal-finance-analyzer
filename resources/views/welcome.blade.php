<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Finance Analyzer') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-100">

    <!-- Navbar -->
    <header class="w-full px-6 py-4 flex justify-between items-center max-w-7xl mx-auto">
        <h1 class="text-lg font-semibold">💰 Finance Analyzer</h1>

        @if (Route::has('login'))
            <nav class="flex items-center gap-4">
                @auth
                    <a href="{{ url('/dashboard') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="text-sm hover:underline">Login</a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md">
                            Get Started
                        </a>
                    @endif
                @endauth
            </nav>
        @endif
    </header>

    <!-- Hero Section -->
    <section class="text-center py-[10rem] px-6">
        <h2 class="text-4xl font-bold mb-4">
            Take Control of Your Money
        </h2>
        <p class="text-gray-600 dark:text-gray-400 max-w-xl mx-auto mb-6">
            Track expenses, analyze spending patterns, and get smart insights — all in one place.
        </p>

        @guest
            <div class="flex justify-center gap-4">
                <a href="{{ route('register') }}" class="px-6 py-3 bg-indigo-600 text-white rounded-lg">
                    Start Free
                </a>
                <a href="{{ route('login') }}" class="px-6 py-3 border rounded-lg">
                    Demo Login
                </a>
            </div>

            <p class="mt-4 text-sm text-gray-500">
                Demo: <b>demo@example.com</b> / <b>password</b>
            </p>
        @endguest
    </section>

    <!-- Features -->
    <section class="max-w-6xl mx-auto px-6 py-16 grid md:grid-cols-3 gap-8">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow">
            <h3 class="font-semibold text-lg mb-2">📊 Smart Analytics</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Visualize your spending habits with clean charts and reports.
            </p>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow">
            <h3 class="font-semibold text-lg mb-2">💸 Expense Tracking</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Easily log and categorize your daily expenses.
            </p>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow">
            <h3 class="font-semibold text-lg mb-2">📈 Insights</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Discover trends and improve your financial decisions.
            </p>
        </div>
    </section>

    <!-- Footer -->
    <footer class="text-center text-sm text-gray-500 py-6">
        © {{ date('Y') }} Finance Analyzer. Built with Laravel.
    </footer>

</body>
</html>