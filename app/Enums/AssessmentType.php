<?php

namespace App\Enums;

enum AssessmentType: int
{
    case RED = 1;
    case YELLOW = 2;
    case GREEN = 3;

    public static function options(): array
    {
        return [
            self::RED->value => self::description(self::RED),
            self::YELLOW->value => self::description(self::YELLOW),
            self::GREEN->value => self::description(self::GREEN),
        ];
    }

    public static function description(AssessmentType $type): string
    {
        return match ($type) {
            self::RED => 'RED',
            self::YELLOW => 'YELLOW',
            self::GREEN => 'GREEN',
        };
    }

    public static function color(AssessmentType $type): string
    {
        return match ($type) {
            self::RED => 'danger',
            self::YELLOW => 'warning',
            self::GREEN => 'success',
        };
    }

    public static function icon(SleepQualityType $type): string
    {
        return match ($type) {
            self::GREEN => 'heroicon-m-shield-check',
            self::YELLOW => 'heroicon-m-bell-alert',
            self::RED => 'heroicon-m-minus-circle',
        };
    }
}
