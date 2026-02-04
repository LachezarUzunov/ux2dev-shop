<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PartnerKey extends Model
{
    protected $fillable = ['partner_id', 'key_hash', 'key_prefix', 'rate_limit_plan', 'expires_at', 'revoked_at'];

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }
}
