<?php

namespace App\Enums;

enum Plan: string
{
    case Free = 'free';
    case Team = 'team';
    case Pro = 'pro';

    public function projectLimit(): ?int
    {
        return match ($this) {
            self::Free => 3,
            self::Team => 20,
            self::Pro => null,
        };
    }
}
