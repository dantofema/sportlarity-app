<?php

namespace App\Enums;

enum ActivityType: int
{

    case REST = 1;
    case TRAINING = 3;
    case COMPETENCE = 4;

    public static function options(): array
    {
        return [
            self::REST->value => self::description(self::REST),
            self::TRAINING->value => self::description(self::TRAINING),
            self::COMPETENCE->value => self::description(self::COMPETENCE),
        ];
    }

    public static function description(ActivityType $type): string
    {
        return match ($type) {
            self::REST => 'DESCANSO',
            self::TRAINING => 'ENTRENAMIENTO',
            self::COMPETENCE => 'COMPETENCIA',
        };
    }

    public static function color(ActivityType $type): string
    {
        return 'info';
    }

    public static function icon(ActivityType $type): string
    {
        return match ($type) {
            self::REST => 'heroicon-m-check-circle',
            self::TRAINING => 'heroicon-m-shield-check',
            self::COMPETENCE => 'heroicon-m-bell-alert',
        };
    }
}
