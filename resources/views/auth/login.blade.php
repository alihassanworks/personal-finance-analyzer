@extends('layouts.guest')

@section('title', 'Log in')

@section('content')
    <h1 class="text-2xl font-semibold tracking-tight text-slate-900 dark:text-white">Welcome back</h1>
    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Sign in to view your dashboard.</p>

    <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-5">
        @csrf
        <div>
            <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                class="mt-1 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
        </div>
        <div>
            <label for="password" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Password</label>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                class="mt-1 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
        </div>
        <div class="flex items-center gap-2">
            <input id="remember" type="checkbox" name="remember" class="rounded border-slate-300 text-indigo-600 dark:border-slate-600">
            <label for="remember" class="text-sm text-slate-600 dark:text-slate-400">Remember me</label>
        </div>
        <button type="submit" class="w-full rounded-lg bg-indigo-600 py-2.5 text-sm font-semibold text-white shadow hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900">
            Log in
        </button>
    </form>
@endsection

@section('footer_links')
    <a href="{{ route('register') }}" class="font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">Create an account</a>
    ·
    <a href="{{ route('home') }}" class="text-slate-500 hover:text-slate-700 dark:hover:text-slate-300">Home</a>
@endsection
