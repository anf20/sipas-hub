<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

#[Fillable(['name', 'start_date', 'end_date', 'is_active'])]
class AcademicYear extends Model
{
    use HasFactory;

    /**
     * Get the classes for the academic year.
     */
    public function schoolClasses(): HasMany
    {
        return $this->hasMany(SchoolClass::class);
    }

    /**
     * Get all students for the academic year through school classes.
     */
    public function students(): HasManyThrough
    {
        return $this->hasManyThrough(Student::class, SchoolClass::class);
    }
}
