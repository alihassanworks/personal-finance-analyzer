<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{ auth()->check() && auth()->user()->prefersDarkMode() ? 'dark' : '' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('vite')
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 antialiased dark:bg-slate-950 dark:text-slate-100">
    @auth
        <header class="border-b border-slate-200 bg-white/90 backdrop-blur dark:border-slate-800 dark:bg-slate-900/90">
            <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-3">
                    <a href="{{ route('dashboard') }}" class="text-lg font-semibold tracking-tight text-indigo-600 dark:text-indigo-400">
                        {{ config('app.name') }}
                    </a>
                    <span class="hidden text-sm text-slate-500 dark:text-slate-400 sm:inline">Smart expense tracker</span>
                </div>
                <nav class="flex flex-wrap items-center gap-1 text-sm font-medium">
                    <a href="{{ route('dashboard') }}" class="rounded-lg px-3 py-2 text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800 {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/50 dark:text-indigo-300' : '' }}">Dashboard</a>
                    <a href="{{ route('transactions.index') }}" class="rounded-lg px-3 py-2 text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800 {{ request()->routeIs('transactions.*') && ! request()->routeIs('transactions.import') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/50 dark:text-indigo-300' : '' }}">Transactions</a>
                    <a href="{{ route('transactions.import') }}" class="rounded-lg px-3 py-2 text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800 {{ request()->routeIs('transactions.import') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/50 dark:text-indigo-300' : '' }}">CSV import</a>
                    <a href="{{ route('categories.thresholds') }}" class="rounded-lg px-3 py-2 text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800 {{ request()->routeIs('categories.thresholds*') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/50 dark:text-indigo-300' : '' }}">Budgets</a>
                </nav>
                <div class="flex flex-wrap items-center gap-2">
                    <form action="" method="post" class="inline">
                        @csrf
                        @method('PATCH')
                        @if(auth()->user()->prefersDarkMode())
                            <input type="hidden" name="theme" value="light">
                            <button type="submit" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">Light mode</button>
                        @else
                            <input type="hidden" name="theme" value="dark">
                            <button type="submit" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">Dark mode</button>
                        @endif
                    </form>
                    <span class="hidden text-sm text-slate-500 dark:text-slate-400 sm:inline">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="rounded-lg bg-slate-900 px-3 py-1.5 text-xs font-medium text-white hover:bg-slate-800 dark:bg-indigo-600 dark:hover:bg-indigo-500">Log out</button>
                    </form>
                </div>
            </div>
        </header>
    @endauth

    <main class="@auth mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8 @else @endauth">
        @if (session('status'))
            <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-900 dark:bg-emerald-950/40 dark:text-emerald-200" role="status">
                {{ session('status') }}
            </div>
        @endif

        @if (session('import_errors') && count(session('import_errors')))
            <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900 dark:border-amber-900 dark:bg-amber-950/40 dark:text-amber-100">
                <p class="font-medium">Import skipped rows</p>
                <ul class="mt-2 list-inside list-disc">
                    @foreach (session('import_errors') as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 dark:border-red-900 dark:bg-red-950/40 dark:text-red-200">
                <ul class="list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
