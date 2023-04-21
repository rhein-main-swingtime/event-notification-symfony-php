<?php
declare(strict_types=1);

namespace App\Enum;

enum Weekdays: int {
    case Montag = 1;
    case Dienstag = 2;
    case Mittwoch = 3;
    case Donnerstag = 4;
    case Freitag = 5;
    case Samstag = 6;
    case Sonntag = 0;

    public static function isFriday(int $weekdayInt): bool {
        return self::from($weekdayInt) == self::Freitag;
    }
}