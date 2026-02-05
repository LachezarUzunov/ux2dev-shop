<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Partner extends Model
{
    protected $fillable = ['name', 'details', 'is_active'];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function partnerKeys(): HasMany
    {
        return $this->hasMany(PartnerKey::class);
    }

    public function idempotencyKeys(): HasMany
    {
        return $this->hasMany(IdempotencyKey::class);
    }
}
