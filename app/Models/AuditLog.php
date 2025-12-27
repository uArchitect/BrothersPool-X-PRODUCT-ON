<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Request;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'model_type',
        'model_id',
        'action',
        'old_values',
        'new_values',
        'user_id',
        'user_name',
        'ip_address',
        'user_agent',
        'route',
        'description',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    /**
     * Get the user who made the change
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Create audit log entry
     */
    public static function log(string $modelType, $modelId, string $action, array $oldValues = null, array $newValues = null, $userId = null): self
    {
        $user = auth()->user();

        return static::create([
            'model_type' => $modelType,
            'model_id' => $modelId,
            'action' => $action,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'user_id' => $userId ?? $user?->id,
            'user_name' => $user?->name,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'route' => Request::route()?->getName(),
            'description' => static::generateDescription($modelType, $modelId, $action, $newValues),
        ]);
    }

    /**
     * Generate human-readable description
     */
    private static function generateDescription(string $modelType, $modelId, string $action, array $newValues = null): string
    {
        $modelName = class_basename($modelType);

        return match($action) {
            'created' => "{$modelName} #{$modelId} oluşturuldu",
            'updated' => "{$modelName} #{$modelId} güncellendi",
            'deleted' => "{$modelName} #{$modelId} silindi",
            default => "{$modelName} #{$modelId} üzerinde işlem yapıldı",
        };
    }

    /**
     * Scope for model-specific logs
     */
    public function scopeForModel($query, string $modelType, $modelId = null)
    {
        $query->where('model_type', $modelType);

        if ($modelId) {
            $query->where('model_id', $modelId);
        }

        return $query;
    }

    /**
     * Scope for user-specific logs
     */
    public function scopeForUser($query, $userId = null)
    {
        $userId = $userId ?? auth()->id();

        return $query->where('user_id', $userId);
    }

    /**
     * Scope for action-specific logs
     */
    public function scopeForAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Get changed fields for updates
     */
    public function getChangedFields(): array
    {
        if (!$this->old_values || !$this->new_values) {
            return [];
        }

        return array_keys(array_diff_assoc($this->new_values, $this->old_values));
    }

    /**
     * Get the model instance that was changed
     */
    public function getModel()
    {
        if (class_exists($this->model_type)) {
            return $this->model_type::find($this->model_id);
        }

        return null;
    }
}