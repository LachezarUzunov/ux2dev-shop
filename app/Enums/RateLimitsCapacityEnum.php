<?php

namespace App\Enums;

use Illuminate\Support\Collection;

enum RateLimitsCapacityEnum: int
{
    case Global         = 1200;
    case GlobalIp       = 300;
    case PartnerPro     = 240;
    case PartnerStart   = 60;
    case PartnerIp      = 30;

    public static function collection(): Collection
    {
        return collect(self::cases());
    }

    public static function fromName(string $name): self
    {
        foreach (self::cases() as $case) {
            if($name === $case->name )
                return $case;
        }

        throw new \InvalidArgumentException('Invalid refill capacity name');
    }
}
