@php
    $tx = $transaction ?? null;
@endphp
<div>
    <label for="type" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Type</label>
    <select id="type" name="type" required
        class="mt-1 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-600 dark:bg-slate-800 dark:text-white">
        <option value="income" @selected(old('type', $tx?->type) === 'income')>Income</option>
        <option value="expense" @selected(old('type', $tx?->type) === 'expense')>Expense</option>
    </select>
</div>
<div>
    <label for="category_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Category</label>
    <select id="category_id" name="category_id" required
        class="mt-1 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-600 dark:bg-slate-800 dark:text-white">
        @foreach ($categories->groupBy('type') as $type => $group)
            <optgroup label="{{ ucfirst($type) }}">
                @foreach ($group as $cat)
                    <option value="{{ $cat->id }}" data-type="{{ $cat->type }}" @selected((string) old('category_id', $tx?->category_id) === (string) $cat->id)>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </optgroup>
        @endforeach
    </select>
    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Category type must match transaction type.</p>
</div>
<div>
    <label for="amount" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Amount</label>
    <input id="amount" type="number" name="amount" step="0.01" min="0.01" required value="{{ old('amount', $tx?->amount) }}"
        class="mt-1 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-600 dark:bg-slate-800 dark:text-white">
</div>
<div>
    <label for="transaction_date" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Date</label>
    <input id="transaction_date" type="date" name="transaction_date" required value="{{ old('transaction_date', $tx?->transaction_date?->format('Y-m-d')) }}"
        class="mt-1 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-600 dark:bg-slate-800 dark:text-white">
</div>
<div>
    <label for="notes" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Notes (optional)</label>
    <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-600 dark:bg-slate-800 dark:text-white">{{ old('notes', $tx?->notes) }}</textarea>
</div>
<div class="flex gap-3">
    <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">{{ $submit }}</button>
    <a href="{{ route('transactions.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium dark:border-slate-600">Cancel</a>
</div>
