<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\BelongsToMasjid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    use BelongsToMasjid;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'action',
        'model',
        'model_id',
        'changes',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'changes' => 'array',
            'created_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (AuditLog $log) {
            $log->created_at = now();
        });
    }

    // ==================== RELATIONSHIPS ====================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ==================== SCOPES ====================

    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByModel($query, string $model)
    {
        return $query->where('model', $model);
    }

    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    public function scopeRecent($query)
    {
        return $query->orderByDesc('created_at');
    }

    // ==================== ACCESSORS ====================

    public function getActionLabelAttribute(): string
    {
        return match ($this->action) {
            'create' => 'Membuat',
            'update' => 'Mengubah',
            'delete' => 'Menghapus',
            'login' => 'Login',
            'logout' => 'Logout',
            'verify' => 'Memverifikasi',
            'reject' => 'Menolak',
            default => ucfirst($this->action),
        };
    }

    public function getModelNameAttribute(): string
    {
        return class_basename($this->model);
    }

    // ==================== STATIC METHODS ====================

    /**
     * Log an action.
     */
    public static function log(
        int $userId,
        string $action,
        string $model,
        string $modelId,
        ?array $changes = null
    ): self {
        return static::create([
            'user_id' => $userId,
            'action' => $action,
            'model' => $model,
            'model_id' => $modelId,
            'changes' => $changes,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Log a model creation.
     */
    public static function logCreate(int $userId, Model $model): self
    {
        return static::log(
            $userId,
            'create',
            $model::class,
            (string) $model->getKey(),
            $model->toArray()
        );
    }

    /**
     * Log a model update.
     */
    public static function logUpdate(int $userId, Model $model): self
    {
        return static::log(
            $userId,
            'update',
            $model::class,
            (string) $model->getKey(),
            [
                'old' => $model->getOriginal(),
                'new' => $model->getChanges(),
            ]
        );
    }

    /**
     * Log a model deletion.
     */
    public static function logDelete(int $userId, Model $model): self
    {
        return static::log(
            $userId,
            'delete',
            $model::class,
            (string) $model->getKey(),
            $model->toArray()
        );
    }
}
