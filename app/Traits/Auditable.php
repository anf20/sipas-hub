<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait Auditable
{
    public static function booted()
    {
        static::created(function ($model) {
            $model->audit('created', null, $model->getAttributes());
        });

        static::updated(function ($model) {
            $oldValues = array_intersect_key($model->getOriginal(), $model->getDirty());
            $newValues = $model->getDirty();
            
            $model->audit('updated', $oldValues, $newValues);
        });

        static::deleted(function ($model) {
            $model->audit('deleted', $model->getAttributes(), null);
        });
    }

    protected function audit(string $action, ?array $oldValues, ?array $newValues)
    {
        // Don't audit if not logged in (e.g. from CLI unless we want to track it)
        $userId = Auth::id();
        
        AuditLog::create([
            'user_id' => $userId,
            'action' => $action,
            'model_type' => get_class($this),
            'model_id' => $this->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip' => Request::ip(),
        ]);
    }
}
