<?php

namespace App\Enums;

enum QuizRoomRoles: int
{
    case HOST = 0;
    case PARTICIPANT = 1;

    public static function toName($value): string
    {
        return match ($value) {
            self::HOST->value => 'Host',
            self::PARTICIPANT->value => 'Participant',
        };
    }
}