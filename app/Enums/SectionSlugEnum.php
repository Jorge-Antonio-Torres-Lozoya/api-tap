<?php

namespace App\Enums;

enum SectionSlugEnum: string
{
    case PRODUCTS = 'products';
    case USERS    = 'users';
    case PROFILES = 'profiles';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
