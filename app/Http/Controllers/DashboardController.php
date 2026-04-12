<?php

namespace App\Http\Controllers;

use App\Services\FinancialAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $analytics = new FinancialAnalyticsService($user);

        $range = $analytics->resolveDateRange(
            $request->query('from'),
            $request->query('to')
        );

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
}
