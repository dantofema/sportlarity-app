<?php

namespace App\Enums;

enum SleepQualityType: int
{

    case EXCELLENT = 4;
    case GOOD = 3;
    case REGULAR = 2;
    case POOR = 1;

    public static function options(): array
    {
        return [
            self::EXCELLENT->value => self::description(self::EXCELLENT),
            self::GOOD->value => self::description(self::GOOD),
            self::REGULAR->value => self::description(self::REGULAR),
            self::POOR->value => self::description(self::POOR),
        ];
    }

    public static function description(SleepQualityType $type): string
    {
        return match ($type) {
            self::EXCELLENT => 'EXCELENTE',
            self::GOOD => 'MUY BIEN',
            self::REGULAR => 'REGULAR',
            self::POOR => 'MAL',
        };
    }

    public static function color(SleepQualityType $type): string
    {
        return match ($type) {
            self::EXCELLENT => 'success',
            self::GOOD => 'info',
            self::REGULAR => 'warning',
            self::POOR => 'danger',
        };
    }

    public static function icon(SleepQualityType $type): string
    {
        return match ($type) {
            self::EXCELLENT => 'heroicon-m-check-circle',
            self::GOOD => 'heroicon-m-shield-check',
            self::REGULAR => 'heroicon-m-bell-alert',
            self::POOR => 'heroicon-m-minus-circle',
        };
    }
}
