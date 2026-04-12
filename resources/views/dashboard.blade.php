@extends('layouts.app')

@section('title', 'Dashboard')

@push('vite')
    @vite('resources/js/dashboard.js')
@endpush

@section('content')
    <div class="mb-8 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Financial dashboard</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Insights, charts, and alerts for your money.</p>
        </div>
    </div>

    <form method="GET" action="{{ route('dashboard') }}" class="mb-8 flex flex-wrap items-end gap-4 rounded-2xl border border-slate-200 bg-white p-4 dark:border-slate-800 dark:bg-slate-900">
        <div>
            <label for="from" class="block text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">From</label>
            <input type="date" id="from" name="from" value="{{ $from }}"
                class="mt-1 rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-600 dark:bg-slate-800 dark:text-white">
        </div>
        <div>
            <label for="to" class="block text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">To</label>
            <input type="date" id="to" name="to" value="{{ $to }}"
                class="mt-1 rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-600 dark:bg-slate-800 dark:text-white">
        </div>
        <div>
            <label for="category_id" class="block text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">Expense category</label>
            <select id="category_id" name="category_id"
                class="mt-1 min-w-50 rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                <option value="">All categories</option>
                @foreach ($expenseCategories as $cat)
                    <option value="{{ $cat->id }}" @selected((string) $filterCategoryId === (string) $cat->id)>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Apply filters</button>
        <a href="{{ route('dashboard') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 dark:border-slate-600 dark:text-slate-300 dark:hover:bg-slate-800">Reset</a>
    </form>

    @if (count($alerts))
        <div class="mb-8 space-y-3">
            @foreach ($alerts as $alert)
                <div @class([
                    'rounded-xl border px-4 py-3 text-sm',
                    'border-red-200 bg-red-50 text-red-900 dark:border-red-900 dark:bg-red-950/50 dark:text-red-100' => $alert['level'] === 'danger',
                    'border-amber-200 bg-amber-50 text-amber-900 dark:border-amber-900 dark:bg-amber-950/50 dark:text-amber-100' => $alert['level'] === 'warning',
                ])>
                    <span class="font-semibold">{{ $alert['level'] === 'danger' ? 'Alert' : 'Warning' }}:</span>
                    {{ $alert['message'] }}
                </div>
            @endforeach
        </div>
    @endif

    <div class="mb-8 grid gap-4 sm:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <p class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">Total income</p>
            <p class="mt-2 text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ number_format($summary['income'], 2) }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <p class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">Total expenses</p>
            <p class="mt-2 text-2xl font-bold text-rose-600 dark:text-rose-400">{{ number_format($summary['expenses'], 2) }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <p class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">Net savings</p>
            <p class="mt-2 text-2xl font-bold text-slate-900 dark:text-white">{{ number_format($summary['net'], 2) }}</p>
            @if ($summary['savings_rate'] !== null)
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Savings rate: <span class="font-semibold text-indigo-600 dark:text-indigo-400">{{ $summary['savings_rate'] }}%</span></p>
            @endif
        </div>
    </div>

    <div class="mb-8 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Smart insights</h2>
        <ul class="mt-4 space-y-3">
            @forelse ($insights as $insight)
                <li class="flex gap-3 rounded-xl bg-slate-50 px-4 py-3 text-sm text-slate-700 dark:bg-slate-800/60 dark:text-slate-200">
                    <span class="mt-0.5 inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-xs font-bold text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300">i</span>
                    <span>{{ $insight['message'] }}</span>
                </li>
            @empty
                <li class="text-sm text-slate-500 dark:text-slate-400">Add more transactions to unlock richer insights.</li>
            @endforelse
        </ul>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Monthly spending trend</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">Last 6 months — total expenses</p>
            <div class="relative mt-4 h-64">
                <canvas id="chartTrend"></canvas>
            </div>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Income vs expenses</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">Selected date range</p>
            <div class="relative mt-4 h-64">
                <canvas id="chartBar"></canvas>
            </div>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 lg:col-span-2">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Category breakdown</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">Expenses by category in range</p>
            <div class="relative mx-auto mt-4 h-72 max-w-md">
                <canvas id="chartPie"></canvas>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="application/json" id="chart-trend-data">@json($chartTrend)</script>
    <script type="application/json" id="chart-pie-data">@json($chartPie)</script>
    <script type="application/json" id="chart-bar-data">@json($chartBar)</script>
@endpush
