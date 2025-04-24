<?php

namespace App\Enums;

enum Role: string
{
    case ADMIN = 'admin';
    case WORKER = 'worker';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Admin',
            self::WORKER => 'Worker',
        };
    }
}
