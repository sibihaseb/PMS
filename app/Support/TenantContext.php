<?php

namespace App\Support;

class TenantContext
{
    public static ?int $organizationId = null;

    public static function set(?int $organizationId): void
    {
        static::$organizationId = $organizationId;
    }

    public static function get(): ?int
    {
        return static::$organizationId;
    }

    public static function clear(): void
    {
        static::$organizationId = null;
    }
}
