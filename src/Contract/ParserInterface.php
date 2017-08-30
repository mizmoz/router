<?php

namespace Mizmoz\Router\Contract;

use Mizmoz\Router\Contract\Parser\ResultInterface;

interface ParserInterface
{
    /**
     * Match the uri with the match argument and return a match Result
     *
     * @param string $uri
     * @return ResultInterface
     */
    public function match(string $uri): ResultInterface;
}