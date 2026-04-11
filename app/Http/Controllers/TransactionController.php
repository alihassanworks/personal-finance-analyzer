<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $analytics = new \App\Services\FinancialAnalyticsService($user);
        $range = $analytics->resolveDateRange(
            $request->query('from'),
            $request->query('to')
        );
        $categoryId = $request->query('category_id');
        $categoryId = $categoryId !== null && $categoryId !== '' ? (int) $categoryId : null;

        $q = Transaction::query()
            ->where('user_id', $user->id)
            ->with('category')
            ->whereBetween('transaction_date', [$range['from']->toDateString(), $range['to']->toDateString()])
            ->when($categoryId, fn ($b) => $b->where('category_id', $categoryId))
            ->orderByDesc('transaction_date')
            ->orderByDesc('id');

        $transactions = $q->paginate(15)->withQueryString();
        $summary = $analytics->summary($range['from'], $range['to']);

        return view('transactions.index', [
            'transactions' => $transactions,
            'from' => $range['from']->toDateString(),
            'to' => $range['to']->toDateString(),
            'filterCategoryId' => $categoryId,
            'expenseCategories' => $user->categories()->orderBy('type')->orderBy('name')->get(),
            'summary' => $summary,
        ]);
    }

    public function create(Request $request): View
    {
        return view('transactions.create', [
            'categories' => $request->user()->categories()->orderBy('type')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedTransaction($request);
        $category = Category::query()
            ->where('user_id', $request->user()->id)
            ->findOrFail($data['category_id']);

        if ($category->type !== $data['type']) {
            return back()->withErrors(['category_id' => 'Category type must match transaction type.'])->withInput();
        }

        Transaction::query()->create([
            'user_id' => $request->user()->id,
            'category_id' => $data['category_id'],
            'type' => $data['type'],
            'amount' => $data['amount'],
            'transaction_date' => $data['transaction_date'],
            'notes' => $data['notes'] ?? null,
        ]);

        return redirect()->route('transactions.index')->with('status', 'Transaction added.');
    }

    public function edit(Request $request, Transaction $transaction): View
    {
        return view('transactions.edit', [
            'transaction' => $transaction,
            'categories' => $request->user()->categories()->orderBy('type')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Transaction $transaction): RedirectResponse
    {
        $data = $this->validatedTransaction($request);
        $category = Category::query()
            ->where('user_id', $request->user()->id)
            ->findOrFail($data['category_id']);

        if ($category->type !== $data['type']) {
            return back()->withErrors(['category_id' => 'Category type must match transaction type.'])->withInput();
        }

        $transaction->update([
            'category_id' => $data['category_id'],
            'type' => $data['type'],
            'amount' => $data['amount'],
            'transaction_date' => $data['transaction_date'],
            'notes' => $data['notes'] ?? null,
        ]);

        return redirect()->route('transactions.index')->with('status', 'Transaction updated.');
    }

    public function destroy(Transaction $transaction): RedirectResponse
    {
        $transaction->delete();

        return redirect()->route('transactions.index')->with('status', 'Transaction deleted.');
    }

    /**
     * @return array{category_id: int, type: string, amount: float, transaction_date: string, notes: ?string}
     */
    protected function validatedTransaction(Request $request): array
    {
        return $request->validate([
            'category_id' => [
                'required',
                'integer',
                Rule::exists('categories', 'id')->where(fn ($q) => $q->where('user_id', $request->user()->id)),
            ],
            'type' => ['required', 'in:income,expense'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:999999999999.99'],
            'transaction_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);
    }

}
