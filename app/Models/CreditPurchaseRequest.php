<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreditPurchaseRequest extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    public const METHOD_TRANSFER = 'transfer';

    public const METHOD_CASH = 'cash';

    protected $fillable = [
        'user_id',
        'credit_package_id',
        'quoted_base_price',
        'quoted_final_price',
        'payment_method',
        'status',
        'requested_tenant_id',
        'requested_date',
        'requested_time_slot',
        'notes',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
    ];

    protected function casts(): array
    {
        return [
            'requested_date' => 'date',
            'reviewed_at' => 'datetime',
            'quoted_base_price' => 'decimal:2',
            'quoted_final_price' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(CreditPackage::class, 'credit_package_id');
    }

    public function requestedTenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'requested_tenant_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
