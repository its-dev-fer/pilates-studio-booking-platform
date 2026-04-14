<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;

class CreditPackagePromotion extends Model
{
    public const TYPE_PERCENT = 'percent';

    public const TYPE_FIXED = 'fixed';

    protected $fillable = [
        'credit_package_id',
        'starts_at',
        'ends_at',
        'type',
        'discount_percent',
        'promotional_price',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'discount_percent' => 'decimal:2',
            'promotional_price' => 'decimal:2',
        ];
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(CreditPackage::class, 'credit_package_id');
    }

    /** @return 'active'|'scheduled'|'ended' */
    public function statusKey(): string
    {
        $now = now();

        if ($this->ends_at->lt($now)) {
            return 'ended';
        }

        if ($this->starts_at->gt($now)) {
            return 'scheduled';
        }

        return 'active';
    }

    public function statusLabel(): string
    {
        return match ($this->statusKey()) {
            'ended' => 'Finalizada',
            'scheduled' => 'Programada',
            default => 'En curso',
        };
    }

    public function ruleSummary(): string
    {
        if ($this->type === self::TYPE_PERCENT) {
            $pct = (float) $this->discount_percent;

            return sprintf($pct == floor($pct) ? '%.0f%% de descuento' : '%.2f%% de descuento', $pct);
        }

        return sprintf('$%s MXN (precio fijo)', number_format((float) $this->promotional_price, 2));
    }

    public static function overlapsWindow(int $creditPackageId, Carbon $startsAt, Carbon $endsAt, ?int $ignoreId = null): bool
    {
        return self::query()
            ->where('credit_package_id', $creditPackageId)
            ->when($ignoreId !== null, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->where('starts_at', '<=', $endsAt)
            ->where('ends_at', '>=', $startsAt)
            ->exists();
    }

    /**
     * @param  array{type: string, discount_percent?: mixed, promotional_price?: mixed}  $data
     */
    public static function validateWindowOrFail(
        int $creditPackageId,
        Carbon $startsAt,
        Carbon $endsAt,
        string $type,
        ?string $discountPercent,
        ?string $promotionalPrice,
        ?int $ignoreId = null
    ): void {
        if ($startsAt->copy()->startOfDay()->lt(today()->startOfDay())) {
            throw ValidationException::withMessages([
                'starts_at' => 'La fecha de inicio no puede ser anterior a hoy.',
            ]);
        }

        if ($endsAt->lessThanOrEqualTo($startsAt)) {
            throw ValidationException::withMessages([
                'ends_at' => 'La fecha y hora de fin debe ser posterior al inicio.',
            ]);
        }

        if ($type === self::TYPE_PERCENT) {
            $pct = $discountPercent !== null && $discountPercent !== '' ? (float) $discountPercent : null;
            if ($pct === null || $pct <= 0 || $pct > 100) {
                throw ValidationException::withMessages([
                    'discount_percent' => 'Indica un porcentaje de descuento entre 0.01 y 100.',
                ]);
            }
        } elseif ($type === self::TYPE_FIXED) {
            $fixed = $promotionalPrice !== null && $promotionalPrice !== '' ? (float) $promotionalPrice : null;
            if ($fixed === null || $fixed <= 0) {
                throw ValidationException::withMessages([
                    'promotional_price' => 'Indica un precio promocional mayor a cero.',
                ]);
            }
        } else {
            throw ValidationException::withMessages([
                'type' => 'Tipo de promoción no válido.',
            ]);
        }

        if (self::overlapsWindow($creditPackageId, $startsAt, $endsAt, $ignoreId)) {
            throw ValidationException::withMessages([
                'starts_at' => 'Este paquete ya tiene otra promoción que se traslapa con este rango de fechas.',
            ]);
        }
    }
}
