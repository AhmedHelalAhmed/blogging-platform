<?php

namespace App\enums;

enum SortByPublicationDate: int
{
    case OLD_TO_NEW = 0;
    case NEW_TO_OLD = 1;

    /**
     * @param int $value
     * @return string
     */
    public static function changeValueToDirection(int $value)
    {
        if ($value === self::OLD_TO_NEW->value) {
            return "desc";
        }

        return "asc";
    }

    public static function getOptions()
    {
        return [
            [
                'text' => 'Oldest',
                'value' => self::OLD_TO_NEW,
            ],
            [
                'text' => 'Newest',
                'value' => self::NEW_TO_OLD,
            ]
        ];
    }
}
