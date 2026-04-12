<?php

namespace App\Services;

use App\Models\CategoryThreshold;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class FinancialAnalyticsService
{
    public function __construct(
        protected User $user
    ) {}

    /**
     * @return array{from: \Carbon\Carbon, to: \Carbon\Carbon}
     */
    public function resolveDateRange(?string $from, ?string $to): array
    {
        $toDate = $to ? Carbon::parse($to)->endOfDay() : now()->endOfDay();
        $fromDate = $from
            ? Carbon::parse($from)->startOfDay()
            : $toDate->copy()->startOfMonth()->startOfDay();

        if ($fromDate->gt($toDate)) {
            [$fromDate, $toDate] = [$toDate->copy()->startOfMonth()->startOfDay(), $fromDate->copy()->endOfDay()];
        }

        return ['from' => $fromDate, 'to' => $toDate];
    }

    /**
     * Summary totals for the selected window.
     *
     * @return array{income: float, expenses: float, net: float, savings_rate: float|null}
     */
    public function summary(Carbon $from, Carbon $to): array
    {
        $base = Transaction::query()->where('user_id', $this->user->id)
            ->whereBetween('transaction_date', [$from->toDateString(), $to->toDateString()]);

        $income = (clone $base)->where('type', 'income')->sum('amount');
        $expenses = (clone $base)->where('type', 'expense')->sum('amount');
        $incomeF = (float) $income;
        $expensesF = (float) $expenses;
        $net = $incomeF - $expensesF;
        $savingsRate = $incomeF > 0 ? round(($net / $incomeF) * 100, 1) : null;

        return [
            'income' => $incomeF,
            'expenses' => $expensesF,
            'net' => $net,
            'savings_rate' => $savingsRate,
        ];
    }

    /**
     * Last N calendar months of total expenses (for line chart).
     *
     * @return array{labels: list<string>, values: list<float>}
     */
    public function monthlyExpenseTrend(int $months = 6): array
    {
        $end = now()->startOfMonth();
        $start = $end->copy()->subMonths($months - 1);

        $rows = Transaction::query()
            ->where('user_id', $this->user->id)
            ->where('type', 'expense')
            ->whereBetween('transaction_date', [$start->toDateString(), now()->toDateString()])
            ->selectRaw('DATE_FORMAT(transaction_date, "%Y-%m") as ym, SUM(amount) as total')
            ->groupBy('ym')
            ->orderBy('ym')
            ->pluck('total', 'ym');

        $labels = [];
        $values = [];
        for ($i = 0; $i < $months; $i++) {
            $m = $start->copy()->addMonths($i);
            $key = $m->format('Y-m');
            $labels[] = $m->format('M Y');
            $values[] = round((float) ($rows[$key] ?? 0), 2);
        }

        return ['labels' => $labels, 'values' => $values];
    }

    /**
     * Expense breakdown by category for the filter range (pie chart).
     *
     * @return array{labels: list<string>, values: list<float>, colors: list<string>}
     */
    public function categoryExpenseBreakdown(Carbon $from, Carbon $to, ?int $categoryId = null): array
    {
        $q = Transaction::query()
            ->where('transactions.user_id', $this->user->id)
            ->where('transactions.type', 'expense')
            ->whereBetween('transactions.transaction_date', [$from->toDateString(), $to->toDateString()])
            ->join('categories', 'categories.id', '=', 'transactions.category_id')
            ->when($categoryId, fn ($b) => $b->where('transactions.category_id', $categoryId))
            ->groupBy('categories.id', 'categories.name', 'categories.color')
            ->orderByDesc(DB::raw('SUM(transactions.amount)'))
            ->get([
                'categories.name as name',
                'categories.color as color',
                DB::raw('SUM(transactions.amount) as total'),
            ]);

        return [
            'labels' => $q->pluck('name')->all(),
            'values' => $q->map(fn ($r) => round((float) $r->total, 2))->all(),
            'colors' => $q->pluck('color')->all(),
        ];
    }

    /**
     * Single-period income vs total expenses (bar chart).
     *
     * @return array{labels: list<string>, values: list<float>}
     */
    public function incomeVsExpenseBar(Carbon $from, Carbon $to): array
    {
        $s = $this->summary($from, $to);

        return [
            'labels' => ['Income', 'Expenses'],
            'values' => [$s['income'], $s['expenses']],
        ];
    }

    /**
     * Smart insights for the dashboard card.
     *
     * @return list<array{type: string, message: string}>
     */
    public function insights(Carbon $from, Carbon $to): array
    {
        $out = [];
        $summary = $this->summary($from, $to);

        if ($summary['savings_rate'] !== null) {
            $out[] = [
                'type' => 'savings',
                'message' => 'Your savings rate is '.$summary['savings_rate'].'% for the selected period.',
            ];
        } else {
            $out[] = [
                'type' => 'savings',
                'message' => 'Add income in this period to calculate a savings rate.',
            ];
        }

        $breakdown = $this->categoryExpenseBreakdown($from, $to);
        if (count($breakdown['labels'])) {
            $out[] = [
                'type' => 'top_category',
                'message' => 'Your highest spending category is '.$breakdown['labels'][0].' ('.number_format($breakdown['values'][0], 2).').',
            ];
        }

        $startCurr = now()->startOfMonth();
        $endCurr = now()->endOfMonth();
        $startPrev = $startCurr->copy()->subMonth();
        $endPrev = $startCurr->copy()->subDay();

        $prevByCat = Transaction::query()
            ->where('user_id', $this->user->id)
            ->where('type', 'expense')
            ->whereBetween('transaction_date', [$startPrev->toDateString(), $endPrev->toDateString()])
            ->selectRaw('category_id, SUM(amount) as total')
            ->groupBy('category_id')
            ->pluck('total', 'category_id');

        $currByCat = Transaction::query()
            ->where('user_id', $this->user->id)
            ->where('type', 'expense')
            ->whereBetween('transaction_date', [$startCurr->toDateString(), $endCurr->toDateString()])
            ->selectRaw('category_id, SUM(amount) as total')
            ->groupBy('category_id')
            ->pluck('total', 'category_id');

        $names = $this->user->categories()->where('type', 'expense')->pluck('name', 'id');

        $momRows = [];
        foreach ($currByCat as $cid => $currTotal) {
            $prev = (float) ($prevByCat[$cid] ?? 0);
            $curr = (float) $currTotal;
            if ($prev <= 0 || $curr <= 0) {
                continue;
            }
            $pct = (($curr - $prev) / $prev) * 100;
            if (abs($pct) < 1) {
                continue;
            }
            $momRows[] = ['cid' => $cid, 'pct' => $pct, 'curr' => $curr];
        }
        usort($momRows, fn ($a, $b) => $b['curr'] <=> $a['curr']);
        foreach (array_slice($momRows, 0, 3) as $row) {
            $name = $names[$row['cid']] ?? 'Category';
            $pct = round($row['pct'], 1);
            if ($pct > 0) {
                $out[] = [
                    'type' => 'mom_up',
                    'message' => 'You spent '.$pct.'% more on '.$name.' compared to last month.',
                ];
            } else {
                $out[] = [
                    'type' => 'mom_down',
                    'message' => 'You spent '.abs($pct).'% less on '.$name.' compared to last month.',
                ];
            }
        }

        return array_slice($out, 0, 10);
    }

    /**
     * Alerts (warnings) for the dashboard.
     *
     * @return list<array{level: string, message: string}>
     */
    public function alerts(Carbon $from, Carbon $to): array
    {
        $alerts = [];
        $summary = $this->summary($from, $to);

        if ($summary['expenses'] > $summary['income'] && $summary['income'] > 0) {
            $alerts[] = [
                'level' => 'danger',
                'message' => 'Spending exceeds income in this period by '.number_format($summary['expenses'] - $summary['income'], 2).'.',
            ];
        } elseif ($summary['income'] <= 0 && $summary['expenses'] > 0) {
            $alerts[] = [
                'level' => 'warning',
                'message' => 'You have expenses but no income recorded in this period.',
            ];
        }

        $startCurr = now()->startOfMonth();
        $endCurr = now()->endOfMonth();

        $thresholds = CategoryThreshold::query()
            ->where('user_id', $this->user->id)
            ->with('category')
            ->get();

        foreach ($thresholds as $th) {
            if (! $th->category || $th->category->type !== 'expense') {
                continue;
            }
            $spent = (float) Transaction::query()
                ->where('user_id', $this->user->id)
                ->where('category_id', $th->category_id)
                ->where('type', 'expense')
                ->whereBetween('transaction_date', [$startCurr->toDateString(), $endCurr->toDateString()])
                ->sum('amount');

            if ($spent > (float) $th->monthly_limit) {
                $alerts[] = [
                    'level' => 'warning',
                    'message' => $th->category->name.' is over budget this month ('.number_format($spent, 2).' / '.number_format((float) $th->monthly_limit, 2).').',
                ];
            }
        }

        $mult = (float) config('finance.spending_spike_multiplier', 1.3);
        $startPrev = $startCurr->copy()->subMonth();
        $endPrev = $startCurr->copy()->subDay();

        $expCurr = (float) Transaction::query()
            ->where('user_id', $this->user->id)
            ->where('type', 'expense')
            ->whereBetween('transaction_date', [$startCurr->toDateString(), $endCurr->toDateString()])
            ->sum('amount');

        $expPrev = (float) Transaction::query()
            ->where('user_id', $this->user->id)
            ->where('type', 'expense')
            ->whereBetween('transaction_date', [$startPrev->toDateString(), $endPrev->toDateString()])
            ->sum('amount');

        if ($expPrev > 0 && $expCurr > $expPrev * $mult) {
            $alerts[] = [
                'level' => 'warning',
                'message' => 'Sudden spending spike: this month’s expenses are '.round((($expCurr - $expPrev) / $expPrev) * 100, 1).'% higher than last month.',
            ];
        }

        return $alerts;
    }

    /**
     * Expense categories for filter dropdowns.
     */
    public function expenseCategories(): Collection
    {
        return $this->user->categories()->where('type', 'expense')->orderBy('name')->get();
    }
}
