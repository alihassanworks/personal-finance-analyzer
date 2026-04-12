<?php

namespace App\Http\Controllers;

use App\Models\CategoryThreshold;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryThresholdController extends Controller
{
    /**
     * Expense categories with optional monthly budget limits.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $categories = $user->categories()->where('type', 'expense')->orderBy('name')->get();
        $thresholds = $user->categoryThresholds()->pluck('monthly_limit', 'category_id');

        return view('categories.thresholds', [
            'categories' => $categories,
            'thresholds' => $thresholds,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        /** @var array<string, mixed> $limits */
        $limits = $request->input('limits', []);
        foreach ($limits as $raw) {
            if ($raw === null || $raw === '') {
                continue;
            }
            if (! is_numeric($raw) || (float) $raw < 0 || (float) $raw > 999999999999.99) {
                return back()->withErrors(['limits' => 'Each limit must be a number between 0 and 999,999,999,999.99, or left empty.']);
            }
        }
        $expenseIds = $user->categories()->where('type', 'expense')->pluck('id')->all();

        foreach ($limits as $categoryId => $raw) {
            $categoryId = (int) $categoryId;
            if (! in_array($categoryId, $expenseIds, true)) {
                continue;
            }
            if ($raw === null || $raw === '') {
                CategoryThreshold::query()
                    ->where('user_id', $user->id)
                    ->where('category_id', $categoryId)
                    ->delete();

                continue;
            }

            CategoryThreshold::query()->updateOrCreate(
                ['user_id' => $user->id, 'category_id' => $categoryId],
                ['monthly_limit' => $raw]
            );
        }

        return redirect()->route('categories.thresholds')->with('status', 'Budget limits saved.');
    }
}
