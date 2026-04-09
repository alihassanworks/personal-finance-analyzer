<?php

namespace App\Providers;

use App\Listeners\CreateDefaultCategoriesForUser;
use App\Models\Transaction;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Event::listen(Registered::class, CreateDefaultCategoriesForUser::class);

        Route::bind('transaction', function (string $value) {
            return Transaction::query()
                ->where('user_id', auth()->id())
                ->whereKey($value)
                ->firstOrFail();
        });
    }
}
