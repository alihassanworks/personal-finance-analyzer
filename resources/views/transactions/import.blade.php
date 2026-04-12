@extends('layouts.app')

@section('title', 'CSV import')

@section('content')
    <div class="mx-auto max-w-2xl">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Import transactions (CSV)</h1>
        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Upload a CSV with a header row. Columns: <code class="rounded bg-slate-100 px-1 dark:bg-slate-800">date</code>, <code class="rounded bg-slate-100 px-1 dark:bg-slate-800">type</code> (income or expense), <code class="rounded bg-slate-100 px-1 dark:bg-slate-800">category</code> (exact name), <code class="rounded bg-slate-100 px-1 dark:bg-slate-800">amount</code>, optional <code class="rounded bg-slate-100 px-1 dark:bg-slate-800">notes</code>.</p>

        <div class="mt-6 rounded-xl border border-slate-200 bg-slate-50 p-4 text-xs font-mono text-slate-700 dark:border-slate-700 dark:bg-slate-800/50 dark:text-slate-300">
            date,type,category,amount,notes<br>
            2026-04-15,expense,Food & Dining,45.20,Lunch<br>
            2026-04-01,income,Salary,3500.00,March pay
        </div>

        <form method="POST" action="{{ route('transactions.import') }}" enctype="multipart/form-data" class="mt-8 space-y-6 rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900">
            @csrf
            <div>
                <label for="file" class="block text-sm font-medium text-slate-700 dark:text-slate-300">CSV file</label>
                <input id="file" type="file" name="file" accept=".csv,.txt" required
                    class="mt-1 block w-full text-sm text-slate-600 file:mr-4 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-indigo-700 hover:file:bg-indigo-100 dark:text-slate-400 dark:file:bg-indigo-950 dark:file:text-indigo-300">
            </div>
            <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Import</button>
        </form>
    </div>
@endsection
