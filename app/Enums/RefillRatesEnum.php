<?php

namespace App\Enums;

use Illuminate\Support\Collection;

enum RefillRatesEnum: int
{
    case Global         = 50;
    case GlobalIp       = 10;
    case PartnerProduct = 5;
    case PartnerStart   = 1;

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

        throw new \InvalidArgumentException('Invalid refill rate name');
    }
}
