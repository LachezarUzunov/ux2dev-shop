<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IdempotencyKey extends Model
{
    protected $fillable = [
        'partner_id',
        'idempotency_key',
        'request_hash',
        'response_body',
        'response_code',
        'expires_at'
    ];

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }
}
