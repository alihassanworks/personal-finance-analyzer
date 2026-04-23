@extends('layouts.app')

@section('title', 'Transactions')

@section('content')
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Transactions</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Manage income and expenses.</p>
        </div>
        <a href="{{ route('transactions.create') }}" class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Add transaction</a>
    </div>

    <form method="GET" class="mb-6 flex flex-wrap items-end gap-4 rounded-2xl border border-slate-200 bg-white p-4 dark:border-slate-800 dark:bg-slate-900">
        <div>
            <label class="block text-xs font-medium uppercase text-slate-500 dark:text-slate-400">Date range</label>
            <select
                id="range-filter"
                name="range"
                onchange="this.form.submit()"
                class="mt-1 min-w-50 rounded-lg border border-slate-300 px-3 py-2 text-sm dark:border-slate-600 dark:bg-slate-800 dark:text-white"
            >
                @foreach ($rangeOptions as $value => $label)
                    <option value="{{ $value }}" @selected($rangeKey === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium uppercase text-slate-500 dark:text-slate-400">From</label>
            <input type="date" name="from" value="{{ $from }}" onchange="document.getElementById('range-filter').value = 'custom'" class="mt-1 rounded-lg border border-slate-300 px-3 py-2 text-sm dark:border-slate-600 dark:bg-slate-800 dark:text-white">
        </div>
        <div>
            <label class="block text-xs font-medium uppercase text-slate-500 dark:text-slate-400">To</label>
            <input type="date" name="to" value="{{ $to }}" onchange="document.getElementById('range-filter').value = 'custom'" class="mt-1 rounded-lg border border-slate-300 px-3 py-2 text-sm dark:border-slate-600 dark:bg-slate-800 dark:text-white">
        </div>
        <div>
            <label class="block text-xs font-medium uppercase text-slate-500 dark:text-slate-400">Category</label>
            <select name="category_id" class="mt-1 min-w-45 rounded-lg border border-slate-300 px-3 py-2 text-sm dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                <option value="">All</option>
                @foreach ($expenseCategories as $cat)
                    <option value="{{ $cat->id }}" @selected((string) $filterCategoryId === (string) $cat->id)>{{ $cat->name }} ({{ $cat->type }})</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white dark:bg-indigo-600">Filter</button>
        <a href="{{ route('transactions.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm dark:border-slate-600">Reset</a>
    </form>

    <div class="mb-6 grid gap-4 sm:grid-cols-3">
        <div class="rounded-xl border border-slate-200 bg-white p-4 dark:border-slate-800 dark:bg-slate-900">
            <p class="text-xs text-slate-500 dark:text-slate-400">Income (range)</p>
            <p class="text-lg font-bold text-emerald-600 dark:text-emerald-400">{{ number_format($summary['income'], 2) }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 dark:border-slate-800 dark:bg-slate-900">
            <p class="text-xs text-slate-500 dark:text-slate-400">Expenses (range)</p>
            <p class="text-lg font-bold text-rose-600 dark:text-rose-400">{{ number_format($summary['expenses'], 2) }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 dark:border-slate-800 dark:bg-slate-900">
            <p class="text-xs text-slate-500 dark:text-slate-400">Net</p>
            <p class="text-lg font-bold text-slate-900 dark:text-white">{{ number_format($summary['net'], 2) }}</p>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                <thead class="bg-slate-50 dark:bg-slate-800/80">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Category</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Notes</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse ($transactions as $tx)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-slate-700 dark:text-slate-200">{{ $tx->transaction_date->format('M j, Y') }}</td>
                            <td class="px-4 py-3 text-sm">
                                <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium {{ $tx->type === 'income' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-200' : 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-200' }}">{{ $tx->type }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <span class="inline-flex items-center gap-2">
                                    <span class="h-2.5 w-2.5 shrink-0 rounded-full" style="background-color: {{ $tx->category->color }}"></span>
                                    {{ $tx->category->name }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-right text-sm font-medium {{ $tx->type === 'income' ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                {{ $tx->type === 'expense' ? '−' : '+' }}{{ number_format((float) $tx->amount, 2) }}
                            </td>
                            <td class="max-w-xs truncate px-4 py-3 text-sm text-slate-500 dark:text-slate-400">{{ $tx->notes ?? '—' }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-right text-sm">
                                <a href="{{ route('transactions.edit', $tx) }}" class="text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">Edit</a>
                                <form action="{{ route('transactions.destroy', $tx) }}" method="POST" class="ml-3 inline" onsubmit="return confirm('Delete this transaction?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600 hover:text-rose-500 dark:text-rose-400">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-sm text-slate-500 dark:text-slate-400">No transactions in this range. <a href="{{ route('transactions.create') }}" class="font-medium text-indigo-600 dark:text-indigo-400">Add one</a></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($transactions->hasPages())
            <div class="border-t border-slate-200 px-4 py-3 dark:border-slate-700">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
@endsection
