<?php

namespace App\Enums;

enum PreparationType: int
{
    case VERY_READY = 4;
    case READY = 3;
    case LITTLE_READY = 2;
    case NOT_READY = 1;

    public static function options(): array
    {
        return [
            self::VERY_READY->value => self::description(self::VERY_READY),
            self::READY->value => self::description(self::READY),
            self::LITTLE_READY->value => self::description(self::LITTLE_READY),
            self::NOT_READY->value => self::description(self::NOT_READY),
        ];
    }

    public static function description(PreparationType $type): string
    {
        return match ($type) {
            self::VERY_READY => 'ME SIENTO MUY PREPARADO',
            self::READY => 'ME SIENTO PREPARADO',
            self::LITTLE_READY => 'ME SIENTO POCO PREPARADO',
            self::NOT_READY => 'NO ME SIENTO PREPARADO',
        };
    }

    public static function color(PreparationType $type): string
    {
        return match ($type) {
            self::VERY_READY => 'success',
            self::READY => 'info',
            self::LITTLE_READY => 'warning',
            self::NOT_READY => 'danger',
        };
    }

    public static function icon(PreparationType $type): string
    {
        return match ($type) {
            self::VERY_READY => 'heroicon-m-check-circle',
            self::READY => 'heroicon-m-shield-check',
            self::LITTLE_READY => 'heroicon-m-bell-alert',
            self::NOT_READY => 'heroicon-m-minus-circle',
        };
    }
}
