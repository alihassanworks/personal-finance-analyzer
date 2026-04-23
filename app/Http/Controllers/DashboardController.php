<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Services\FinancialAnalyticsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $analytics = new FinancialAnalyticsService($user);
        $rangeKey = (string) $request->query('range', 'this_month');

        $range = $this->resolvePresetRange($rangeKey, $user->id);
        if ($range === null) {
            $rangeKey = 'custom';
            $range = $analytics->resolveDateRange(
                $request->query('from'),
                $request->query('to')
            );
        }

        $categoryId = $request->query('category_id');
        $categoryId = $categoryId !== null && $categoryId !== '' ? (int) $categoryId : null;

        $summary = $analytics->summary($range['from'], $range['to']);
        $trend = $analytics->monthlyExpenseTrend(6);
        $pie = $analytics->categoryExpenseBreakdown($range['from'], $range['to'], $categoryId);
        $bar = $analytics->incomeVsExpenseBar($range['from'], $range['to']);
        $insights = $analytics->insights($range['from'], $range['to']);
        $alerts = $analytics->alerts($range['from'], $range['to']);
        $expenseCategories = $analytics->expenseCategories();

        return view('dashboard', [
            'from' => $range['from']->toDateString(),
            'to' => $range['to']->toDateString(),
            'rangeKey' => $rangeKey,
            'rangeOptions' => $this->rangeOptions(),
            'summary' => $summary,
            'chartTrend' => $trend,
            'chartPie' => $pie,
            'chartBar' => $bar,
            'insights' => $insights,
            'alerts' => $alerts,
            'expenseCategories' => $expenseCategories,
            'filterCategoryId' => $categoryId,
        ]);
    }

    /**
     * @return array<string, string>
     */
    protected function rangeOptions(): array
    {
        return [
            'last_7_days' => 'Last 7 days',
            'this_month' => 'This month',
            'last_month' => 'Last month',
            'last_2_months' => 'Last 2 months',
            'last_3_months' => 'Last 3 months',
            'last_6_months' => 'Last 6 months',
            'all_time' => 'All time',
            'custom' => 'Custom range',
        ];
    }

    /**
     * @return array{from: Carbon, to: Carbon}|null
     */
    protected function resolvePresetRange(string $rangeKey, int $userId): ?array
    {
        $today = now();

        if ($rangeKey === 'all_time') {
            $firstDate = Transaction::query()
                ->where('user_id', $userId)
                ->min('transaction_date');

            return [
                'from' => $firstDate ? Carbon::parse($firstDate)->startOfDay() : $today->copy()->startOfMonth()->startOfDay(),
                'to' => $today->copy()->endOfDay(),
            ];
        }

        return match ($rangeKey) {
            'last_7_days' => [
                'from' => $today->copy()->subDays(6)->startOfDay(),
                'to' => $today->copy()->endOfDay(),
            ],
            'this_month' => [
                'from' => $today->copy()->startOfMonth()->startOfDay(),
                'to' => $today->copy()->endOfDay(),
            ],
            'last_month' => [
                'from' => $today->copy()->subMonthNoOverflow()->startOfMonth()->startOfDay(),
                'to' => $today->copy()->subMonthNoOverflow()->endOfMonth()->endOfDay(),
            ],
            'last_2_months' => [
                'from' => $today->copy()->subMonthsNoOverflow(2)->startOfDay(),
                'to' => $today->copy()->endOfDay(),
            ],
            'last_3_months' => [
                'from' => $today->copy()->subMonthsNoOverflow(3)->startOfDay(),
                'to' => $today->copy()->endOfDay(),
            ],
            'last_6_months' => [
                'from' => $today->copy()->subMonthsNoOverflow(6)->startOfDay(),
                'to' => $today->copy()->endOfDay(),
            ],
            default => null,
        };
    }
}
