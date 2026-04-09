<?php

namespace App\Listeners;

use App\Models\Category;
use Illuminate\Auth\Events\Registered;

class CreateDefaultCategoriesForUser
{
    /**
     * Seed default income/expense categories when a user registers.
     */
    public function handle(Registered $event): void
    {
        $user = $event->user;

        foreach (config('finance.default_categories', []) as $row) {
            Category::query()->updateOrCreate(
                [
                    'user_id' => $user->id,
                    'slug' => $row['slug'],
                ],
                [
                    'name' => $row['name'],
                    'type' => $row['type'],
                    'color' => $row['color'] ?? '#64748b',
                ]
            );
        }
    }
}
