@extends('layouts.app')

@section('title', 'Add transaction')

@section('content')
    <div class="mx-auto max-w-xl">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Add transaction</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Record income or an expense with a category.</p>

        <form method="POST" action="{{ route('transactions.store') }}" class="mt-8 space-y-6 rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900">
            @csrf
            @include('transactions._form', ['categories' => $categories, 'submit' => 'Create'])
        </form>
    </div>
@endsection
