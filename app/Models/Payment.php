<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use Auditable, SoftDeletes;

    protected $fillable = [
        'invoice_id',
        'amount',
        'method',
        'status',
        'proof_file',
        'paid_at',
        'receipt_number',
        'recorded_by',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class)->withTrashed();
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    /**
     * Get amount in Indonesian words (Terbilang)
     */
    public function getTerbilangAmountAttribute(): string
    {
        return $this->penyebut((int) $this->amount);
    }

    private function penyebut($nilai): string
    {
        $nilai = abs($nilai);
        $huruf = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];
        $temp = '';
        if ($nilai < 12) {
            $temp = ' '.$huruf[$nilai];
        } elseif ($nilai < 20) {
            $temp = $this->penyebut($nilai - 10).' Belas';
        } elseif ($nilai < 100) {
            $temp = $this->penyebut($nilai / 10).' Puluh'.$this->penyebut($nilai % 10);
        } elseif ($nilai < 200) {
            $temp = ' Seratus'.$this->penyebut($nilai - 100);
        } elseif ($nilai < 1000) {
            $temp = $this->penyebut($nilai / 100).' Ratus'.$this->penyebut($nilai % 100);
        } elseif ($nilai < 2000) {
            $temp = ' Seribu'.$this->penyebut($nilai - 1000);
        } elseif ($nilai < 1000000) {
            $temp = $this->penyebut($nilai / 1000).' Ribu'.$this->penyebut($nilai % 1000);
        } elseif ($nilai < 1000000000) {
            $temp = $this->penyebut($nilai / 1000000).' Juta'.$this->penyebut($nilai % 1000000);
        }

        return $temp;
    }
}
