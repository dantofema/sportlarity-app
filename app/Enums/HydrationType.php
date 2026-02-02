<?php

namespace App\Enums;

enum HydrationType: int
{
    case WRONG = 1;
    case REGULAR = 2;
    case VERY_WELL = 3;
    case EXCELLENT = 4;

    public static function options(): array
    {
        return [
            self::WRONG->value => self::description(self::WRONG),
            self::REGULAR->value => self::description(self::REGULAR),
            self::VERY_WELL->value => self::description(self::VERY_WELL),
            self::EXCELLENT->value => self::description(self::EXCELLENT),
        ];
    }

    public static function description(HydrationType $type): string
    {
        return match ($type) {
            self::WRONG => 'MAL',
            self::REGULAR => 'REGULAR',
            self::VERY_WELL => 'MUY BIEN',
            self::EXCELLENT => 'EXCELENTE',
        };
    }

    public static function color(HydrationType $type): string
    {
        return match ($type) {
            self::WRONG => 'danger',
            self::REGULAR => 'warning',
            self::VERY_WELL => 'info',
            self::EXCELLENT => 'success',
        };
    }

    public static function icon(HydrationType $type): string
    {
        return match ($type) {
            self::EXCELLENT => 'heroicon-m-check-circle',
            self::VERY_WELL => 'heroicon-m-shield-check',
            self::REGULAR => 'heroicon-m-bell-alert',
            self::WRONG => 'heroicon-m-minus-circle',
        };
    }
}
