<?php

namespace App\Enums;

enum QuizRoomTeams: int
{
    case TEAM_ONE = 0;
    case TEAM_TWO = 1;
    case TEAM_THREE = 2;
    case TEAM_FOUR = 3;
    case TEAM_FIVE = 4;

    public static function toName($value): string
    {
        return match ($value) {
            self::TEAM_ONE->value => 'First Team',
            self::TEAM_TWO->value => 'Second Team',
            self::TEAM_THREE->value => 'Third Team',
            self::TEAM_FOUR->value => 'Fourth Team',
            self::TEAM_FIVE->value => 'Fifth Team',
        };
    }
}