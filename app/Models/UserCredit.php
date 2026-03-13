<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserCredit extends Model
{
    protected $fillable = [
        'user_id',
        'tenant_id',
        'balance',
        'expires_at',
        'is_special',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'is_special' => 'boolean',
            'balance' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
