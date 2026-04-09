<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['user_id', 'name', 'slug', 'type', 'color'])]
class Category extends Model
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function threshold(): HasOne
    {
        return $this->hasOne(CategoryThreshold::class);
    }

    public function isExpense(): bool
    {
        return $this->type === 'expense';
    }

    public function isIncome(): bool
    {
        return $this->type === 'income';
    }
}
