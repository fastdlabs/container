<?php

/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2020
 *
 * @link      https://www.github.com/janhuang
 * @link      https://www.fastdlabs.com
 */
class StaticInjection
{
    static $date;

    public static function now(DateTime $dateTime)
    {
        static::$date = $dateTime->format(DateTime::W3C);
    }
}
