@extends('layouts.app')

@section('title', 'Budget limits')

@section('content')
    <div class="mb-8">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Category budgets</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Set monthly spending limits per expense category. You’ll get warnings on the dashboard when you exceed them.</p>
    </div>

    <form method="POST" action="{{ route('categories.thresholds.update') }}" class="space-y-6">
        @csrf
        @method('PUT')
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                <thead class="bg-slate-50 dark:bg-slate-800/80">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500 dark:text-slate-400">Category</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500 dark:text-slate-400">Color</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500 dark:text-slate-400">Monthly limit</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    @foreach ($categories as $cat)
                        <tr>
                            <td class="px-4 py-3 text-sm font-medium text-slate-900 dark:text-white">{{ $cat->name }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-block h-4 w-4 rounded-full ring-2 ring-slate-200 dark:ring-slate-600" style="background-color: {{ $cat->color }}"></span>
                            </td>
                            <td class="px-4 py-3">
                                <input type="number" step="0.01" min="0" name="limits[{{ $cat->id }}]"
                                    value="{{ old('limits.'.$cat->id, $thresholds[$cat->id] ?? null) }}"
                                    placeholder="No limit"
                                    class="w-full max-w-xs rounded-lg border border-slate-300 px-3 py-2 text-sm dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Save limits</button>
    </form>
@endsection
