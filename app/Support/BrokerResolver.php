<?php

namespace App\Support;

use App\Models\Property;
use App\Models\User;

class BrokerResolver
{
    private static bool $resolved = false;

    private static ?User $cachedSiteBroker = null;

    public static function siteBroker(): ?User
    {
        if (! self::$resolved) {
            self::$cachedSiteBroker = User::query()
                ->where('is_admin', true)
                ->orderByDesc('updated_at')
                ->orderByDesc('id')
                ->first();
            self::$resolved = true;
        }

        return self::$cachedSiteBroker;
    }

    public static function forProperty(Property $property): ?User
    {
        if ($property->relationLoaded('broker')) {
            return $property->broker ?: self::siteBroker();
        }

        return $property->broker()->first() ?: self::siteBroker();
    }
}
