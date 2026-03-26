<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreSection extends Model
{
    protected $fillable = [
        'tenant_id',
        'name',
        'slug',
        'is_active',
    ];
}
