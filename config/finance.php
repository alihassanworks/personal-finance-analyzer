<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Spending spike detection
    |--------------------------------------------------------------------------
    | Alert when current period total expenses exceed the previous period
    | by this multiplier (e.g. 1.3 = 30% higher).
    */
    'spending_spike_multiplier' => (float) env('FINANCE_SPIKE_MULTIPLIER', 1.3),

    /*
    |--------------------------------------------------------------------------
    | Default categories for new users (created on registration)
    |--------------------------------------------------------------------------
    */
    'default_categories' => [
        ['name' => 'Food & Dining', 'slug' => 'food', 'type' => 'expense', 'color' => '#ef4444'],
        ['name' => 'Rent / Housing', 'slug' => 'rent', 'type' => 'expense', 'color' => '#f97316'],
        ['name' => 'Travel', 'slug' => 'travel', 'type' => 'expense', 'color' => '#8b5cf6'],
        ['name' => 'Utilities', 'slug' => 'utilities', 'type' => 'expense', 'color' => '#06b6d4'],
        ['name' => 'Entertainment', 'slug' => 'entertainment', 'type' => 'expense', 'color' => '#ec4899'],
        ['name' => 'Healthcare', 'slug' => 'healthcare', 'type' => 'expense', 'color' => '#14b8a6'],
        ['name' => 'Shopping', 'slug' => 'shopping', 'type' => 'expense', 'color' => '#eab308'],
        ['name' => 'Other expense', 'slug' => 'other-expense', 'type' => 'expense', 'color' => '#64748b'],
        ['name' => 'Salary', 'slug' => 'salary', 'type' => 'income', 'color' => '#22c55e'],
        ['name' => 'Freelance', 'slug' => 'freelance', 'type' => 'income', 'color' => '#84cc16'],
        ['name' => 'Investments', 'slug' => 'investments', 'type' => 'income', 'color' => '#10b981'],
        ['name' => 'Other income', 'slug' => 'other-income', 'type' => 'income', 'color' => '#94a3b8'],
    ],

];
