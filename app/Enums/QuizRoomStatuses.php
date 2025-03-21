<?php

namespace App\Enums;

enum QuizRoomStatuses: int
{
    case WAITING_FOR_PLAYERS = 0;
    case IN_PROGRESS = 1;
    case COMPLETED = 2;

    public static function toName($value): string
    {
        return match ($value) {
            self::WAITING_FOR_PLAYERS->value => 'Waiting for players',
            self::IN_PROGRESS->value => 'In Progress',
            self::COMPLETED->value => 'Completed',
        };
    }
}