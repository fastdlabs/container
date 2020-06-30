<?php

/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2020
 *
 * @link      https://www.github.com/janhuang
 * @link      https://www.fastdlabs.com
 */
class MethodInjection
{
    public $date;

    public function now(DateTime $date)
    {
        $this->date = $date->format(DateTime::W3C);

        return $this;
    }
}
