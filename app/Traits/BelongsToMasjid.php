<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Masjid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait BelongsToMasjid
{
    public static function bootBelongsToMasjid(): void
    {
        static::creating(function (Model $model) {
            if (! $model->masjid_id && session()->has('active_masjid_id')) {
                $model->masjid_id = session('active_masjid_id');
            }
        });

        static::addGlobalScope('masjid', function (Builder $builder) {
            // Evaluasi session setiap kali query dieksekusi, bukan saat model di-boot
            $activeId = session('active_masjid_id') ?? (auth()->check() ? auth()->user()->masjid_id : 1);

            // If in Admin context (identified by route name or auth), stick to active masjid.
            // Otherwise (Public context), allow inheritance from Pusat (ID 1).
            if (request()->is('admin*') || request()->is('manage*') || auth()->check()) {
                $builder->where($builder->getQuery()->from.'.masjid_id', $activeId);
            } else {
                $builder->where(function (Builder $query) use ($activeId, $builder) {
                    $query->where($builder->getQuery()->from.'.masjid_id', $activeId)
                        ->orWhere($builder->getQuery()->from.'.masjid_id', 1);
                });
            }
        });
    }

    public function masjid(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Masjid::class);
    }
}
