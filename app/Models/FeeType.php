<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Traits\Auditable;

#[Fillable(['name', 'category', 'default_amount', 'is_recurring', 'recurrence', 'applicable_grades', 'is_active'])]
class FeeType extends Model
{
    use HasFactory, Auditable;
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'default_amount' => 'decimal:2',
            'is_recurring' => 'boolean',
            'is_active' => 'boolean',
            'applicable_grades' => 'json',
        ];
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
