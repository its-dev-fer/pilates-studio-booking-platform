<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    protected $fillable = [
        'tenant_id',
        'user_id',
        'date',
        'time_slot',
        'status',
        'check_in_status',
        'checked_in_by',
        'payment_method',
        'booking_origin',
        'credit_purchase_request_id',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function checkedInBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_in_by');
    }

    public function creditPurchaseRequest(): BelongsTo
    {
        return $this->belongsTo(CreditPurchaseRequest::class, 'credit_purchase_request_id');
    }
}
