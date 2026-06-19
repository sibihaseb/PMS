<?php

namespace App\Enums;

enum Plan: string
{
    case Free = 'free';
    case Pro = 'pro';

    public function projectLimit(): ?int
    {
        return match ($this) {
            self::Free => 3,
            self::Pro => null,
        };
    }
}
