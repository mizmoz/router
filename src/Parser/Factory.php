<?php

namespace Mizmoz\Router\Parser;

use Mizmoz\Router\Contract\ParserInterface;

class Factory
{
    /**
     * Parse the string
     *
     * @param string $match
     * @return ParserInterface
     */
    public static function getParser(string $match): ParserInterface
    {
        return new Plain($match);
    }
}