<?php


class GenderType
{
    private static $choices = [
        'ALL' => 'All Gender',
        'MALE' => 'Male',
        'FEMALE' => 'Female',
        'NOT_SAY' => 'Not Say',
    ];

    /**
     * @return string[]
     */
    public static function choices()
    {
        return self::$choices;
    }

    /**
     * @return string[]
     */
    public static function keys()
    {
        return array_keys(self::$choices);
    }
}