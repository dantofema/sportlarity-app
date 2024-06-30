<?php

namespace App\Enums;

enum StrengthTrainingType: int
{

    case REST = 1;
    case LIGHT = 2;
    case MODERATE = 3;
    case INTENSIVE = 4;

    public static function options(): array
    {
        return [
            self::REST->value => self::description(self::REST),
            self::LIGHT->value => self::description(self::LIGHT),
            self::MODERATE->value => self::description(self::MODERATE),
            self::INTENSIVE->value => self::description(self::INTENSIVE),
        ];
    }

    public static function description(StrengthTrainingType $type): string
    {
        return match ($type) {
            self::REST => 'NO ENTRENÉ',
            self::LIGHT => 'LEVE',
            self::MODERATE => 'MODERADO',
            self::INTENSIVE => 'INTENSO',
        };
    }

    public static function color(StrengthTrainingType $type): string
    {
        return match ($type) {
            self::REST => 'success',
            self::LIGHT => 'info',
            self::MODERATE => 'warning',
            self::INTENSIVE => 'danger',
        };
    }

    public static function icon(StrengthTrainingType $type): string
    {
        return match ($type) {
            self::REST => 'heroicon-m-check-circle',
            self::LIGHT => 'heroicon-m-shield-check',
            self::MODERATE => 'heroicon-m-bell-alert',
            self::INTENSIVE => 'heroicon-m-minus-circle',
        };
    }

}
