<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['nis', 'name', 'current_grade', 'school_class_id', 'parent_user_id', 'gender', 'birth_date', 'address', 'photo', 'status', 'entry_year'])]
class Student extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Get the class that owns the student.
     */
    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }

    /**
     * Get the parent that owns the student.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_user_id');
    }

    /**
     * Get the invoices for the student.
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($student) {
            if (empty($student->nis)) {
                $entryYear = $student->entry_year ?? date('Y');
                
                // YY (2 digit tahun masuk)
                $prefix = substr($entryYear, -2);
                
                // XXXX (4 digit urutan)
                $lastStudent = self::where('nis', 'like', $prefix . '%')
                                   ->orderBy('id', 'desc') // Order by ID to ensure sequence continuity
                                   ->first();
                
                $sequence = 1;
                if ($lastStudent) {
                    $lastSequence = (int) substr($lastStudent->nis, -4);
                    $sequence = $lastSequence + 1;
                }
                
                $student->nis = $prefix . str_pad($sequence, 4, '0', STR_PAD_LEFT);
            }
        });
    }
}
