<?php

namespace Mizmoz\Router\Parser;

use Mizmoz\Router\Contract\ParserInterface;

abstract class ParserBase implements ParserInterface
{
    /**
     * @var string
     */
    protected $match;

    /**
     * Plain constructor.
     * @param string $match
     */
    public function __construct(string $match)
    {
        $this->match = $match;
    }
}