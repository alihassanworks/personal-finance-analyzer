@extends('layouts.app')

@section('title', 'Edit transaction')

@section('content')
    <div class="mx-auto max-w-xl">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Edit transaction</h1>

        <form method="POST" action="{{ route('transactions.update', $transaction) }}" class="mt-8 space-y-6 rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900">
            @csrf
            @method('PUT')
            @include('transactions._form', ['categories' => $categories, 'transaction' => $transaction, 'submit' => 'Save changes'])
        </form>
    </div>
@endsection
