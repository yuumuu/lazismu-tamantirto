<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait BelongsToBranch
{
    public static function bootBelongsToBranch(): void
    {
        static::creating(function (Model $model) {
            if (! $model->branch_id && session()->has('active_branch_id')) {
                $model->branch_id = session('active_branch_id');
            }
        });

        static::addGlobalScope('branch', function (Builder $builder) {
            $activeId = session('active_branch_id');

            if (! $activeId && auth()->hasUser()) {
                $activeId = auth()->user()->branch_id;
            }

            if (! $activeId) {
                return;
            }

            if (request()->is('admin*') || request()->is('manage*') || auth()->hasUser()) {
                $builder->where($builder->getQuery()->from.'.branch_id', $activeId);
            } else {
                $builder->where(function (Builder $query) use ($activeId, $builder) {
                    $query->where($builder->getQuery()->from.'.branch_id', $activeId)
                        ->orWhere($builder->getQuery()->from.'.branch_id', 1);
                });
            }
        });
    }

    public function branch(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
