<?php

/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */
class ConstructorInjection
{
    /**
     * @var DateTime
     */
    protected $date;

    public function __construct(DateTime $date)
    {
        $this->date = $date;
    }

    public function now()
    {
        return $this->date->format(DateTime::W3C);
    }
}