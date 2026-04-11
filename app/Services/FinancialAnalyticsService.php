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
}
