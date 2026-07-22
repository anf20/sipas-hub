<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['student_id', 'fee_type_id', 'amount', 'due_date', 'period_month', 'period_year', 'status', 'snap_token', 'notes', 'generated_by'])]
class Invoice extends Model
{
    use Auditable, HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'due_date' => 'date',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class)->withTrashed();
    }

    public function feeType(): BelongsTo
    {
        return $this->belongsTo(FeeType::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function generatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    /**
     * Get the formatted billing detail name.
     * Shows specific month for SPP.
     */
    public function getBillingDetailAttribute(): string
    {
        if ($this->feeType && $this->feeType->category === 'SPP' && $this->period_month) {
            $monthName = \Carbon\Carbon::create()->month($this->period_month)->translatedFormat('F');
            return "SPP Bulan {$monthName} {$this->period_year}";
        }

        return $this->feeType ? $this->feeType->name : 'Tagihan';
    }
}
