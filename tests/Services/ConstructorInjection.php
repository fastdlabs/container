<?php

/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2020
 *
 * @link      https://www.github.com/janhuang
 * @link      https://www.fastdlabs.com
 */
class ConstructorInjection
{
    /**
     * @var DateTime
     */
    public $date;

    public function __construct(DateTime $date)
    {
        $this->date = $date->format(DateTime::W3C);
    }

    public function now()
    {
        return $this->date;
    }
}
