<?php

namespace App\Enums;

enum StressType: int
{
    case LOW = 4;
    case MODERATE = 3;
    case HIGH = 2;
    case VERY_HIGH = 1;

    public static function options(): array
    {
        return [
            self::LOW->value => self::description(self::LOW),
            self::MODERATE->value => self::description(self::MODERATE),
            self::HIGH->value => self::description(self::HIGH),
            self::VERY_HIGH->value => self::description(self::VERY_HIGH),
        ];
    }

    public static function description(StressType $type): string
    {
        return match ($type) {
            self::LOW => 'BAJO',
            self::MODERATE => 'MODERADO',
            self::HIGH => 'ELEVADO',
            self::VERY_HIGH => 'MUY ELEVADO',
        };
    }

    public static function color(StressType $type): string
    {
        return match ($type) {
            self::LOW => 'success',
            self::MODERATE => 'info',
            self::HIGH => 'warning',
            self::VERY_HIGH => 'danger',
        };
    }

    public static function icon(StressType $type): string
    {
        return match ($type) {
            self::LOW => 'heroicon-m-check-circle',
            self::MODERATE => 'heroicon-m-shield-check',
            self::HIGH => 'heroicon-m-bell-alert',
            self::VERY_HIGH => 'heroicon-m-minus-circle',
        };
    }
}
