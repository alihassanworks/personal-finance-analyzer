@extends('layouts.guest')

@section('title', 'Register')

@section('content')
    <h1 class="text-2xl font-semibold tracking-tight text-slate-900 dark:text-white">Create your account</h1>
    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Start tracking income and expenses in minutes.</p>

    <form method="POST" action="{{ route('register') }}" class="mt-8 space-y-5">
        @csrf
        <div>
            <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Name</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                class="mt-1 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
        </div>
        <div>
            <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                class="mt-1 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
        </div>
        <div>
            <label for="password" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Password</label>
            <input id="password" type="password" name="password" required autocomplete="new-password"
                class="mt-1 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
        </div>
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Confirm password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                class="mt-1 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
        </div>
        <button type="submit" class="w-full rounded-lg bg-indigo-600 py-2.5 text-sm font-semibold text-white shadow hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900">
            Register
        </button>
    </form>
@endsection

@section('footer_links')
    <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">Already registered?</a>
@endsection
