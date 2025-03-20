<?php

namespace App\Enums;

enum Role: string
{
    case SUPER_ADMIN = 'super_admin';
    case WELLNESS = 'wellness';
    case COACH = 'coach';
    case PROFESSIONAL = 'professional';
}
