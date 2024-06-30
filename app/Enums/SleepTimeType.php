<?php

namespace App\Enums;

enum SleepTimeType: int
{

    case LESS_THAN_4 = 1;
    case BETWEEN_4_AND_6 = 2;
    case BETWEEN_6_AND_8 = 3;
    case MORE_THAN_8 = 4;

    public static function options(): array
    {
        return [
            self::LESS_THAN_4->value => self::description(self::LESS_THAN_4),
            self::BETWEEN_4_AND_6->value => self::description(self::BETWEEN_4_AND_6),
            self::BETWEEN_6_AND_8->value => self::description(self::BETWEEN_6_AND_8),
            self::MORE_THAN_8->value => self::description(self::MORE_THAN_8),
        ];
    }

    public static function description(SleepTimeType $type): string
    {
        return match ($type) {
            self::LESS_THAN_4 => 'MENOS DE 4',
            self::BETWEEN_4_AND_6 => 'ENTRE 4 Y 6',
            self::BETWEEN_6_AND_8 => 'ENTRE 6 Y 8',
            self::MORE_THAN_8 => 'MÁS DE 8',
        };
    }

    public static function color(SleepTimeType $type): string
    {
        return match ($type) {
            self::LESS_THAN_4 => 'danger',
            self::BETWEEN_4_AND_6 => 'warning',
            self::BETWEEN_6_AND_8 => 'info',
            self::MORE_THAN_8 => 'success',
        };
    }

    public static function icon(SleepTimeType $type): string
    {
        return match ($type) {
            self::MORE_THAN_8 => 'heroicon-m-check-circle',
            self::BETWEEN_6_AND_8 => 'heroicon-m-shield-check',
            self::BETWEEN_4_AND_6 => 'heroicon-m-bell-alert',
            self::LESS_THAN_4 => 'heroicon-m-minus-circle',
        };
    }


}
