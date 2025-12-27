<?php

namespace App\Observers;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

class ModelAuditObserver
{
    /**
     * Handle the Model "created" event.
     */
    public function created(Model $model): void
    {
        // Only audit models that have the auditable trait or specific models
        if ($this->shouldAudit($model)) {
            AuditLog::log(
                get_class($model),
                $model->getKey(),
                'created',
                null,
                $model->getAttributes()
            );
        }
    }

    /**
     * Handle the Model "updated" event.
     */
    public function updated(Model $model): void
    {
        if ($this->shouldAudit($model) && $model->wasChanged()) {
            AuditLog::log(
                get_class($model),
                $model->getKey(),
                'updated',
                $model->getOriginal(),
                $model->getAttributes()
            );
        }
    }

    /**
     * Handle the Model "deleted" event.
     */
    public function deleted(Model $model): void
    {
        if ($this->shouldAudit($model)) {
            AuditLog::log(
                get_class($model),
                $model->getKey(),
                'deleted',
                $model->getOriginal(),
                null
            );
        }
    }

    /**
     * Handle the Model "force deleted" event.
     */
    public function forceDeleted(Model $model): void
    {
        if ($this->shouldAudit($model)) {
            AuditLog::log(
                get_class($model),
                $model->getKey(),
                'force_deleted',
                $model->getOriginal(),
                null
            );
        }
    }

    /**
     * Handle the Model "restored" event.
     */
    public function restored(Model $model): void
    {
        if ($this->shouldAudit($model)) {
            AuditLog::log(
                get_class($model),
                $model->getKey(),
                'restored',
                null,
                $model->getAttributes()
            );
        }
    }

    /**
     * Determine if this model should be audited
     */
    private function shouldAudit(Model $model): bool
    {
        // List of models that should be audited
        $auditableModels = [
            'App\Models\Table',
            'App\Models\MenuItem',
            'App\Models\Order',
            'App\Models\Customer',
            'App\Models\Employee',
            'App\Models\Category',
            'App\Models\Reservation',
        ];

        return in_array(get_class($model), $auditableModels);
    }
}