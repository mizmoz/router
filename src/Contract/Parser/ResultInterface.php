<?php

namespace Mizmoz\Router\Contract\Parser;

use Mizmoz\Router\Contract\RouteInterface;
use Mizmoz\Router\Contract\StackInterface;

interface ResultInterface
{
    /**
     * Not actually a match
     */
    const MATCH_NONE = 'none';

    /**
     * Partial match such as /app is a partial match of the uri /app/dashboard.
     * This would signal that we need to look at the routes children
     */
    const MATCH_PARTIAL = 'partial';

    /**
     * Full match, we're done
     */
    const MATCH_FULL = 'full';

    /**
     * Add items to the stack
     *
     * @param StackInterface $stack
     * @return ResultInterface
     */
    public function addStack(StackInterface $stack): ResultInterface;

    /**
     * Get the match type
     *
     * @return string
     */
    public function match(): string;

    /**
     * Get the matched route
     *
     * @return RouteInterface
     */
    public function getRoute(): RouteInterface;

    /**
     * Get the stack
     *
     * @return StackInterface
     */
    public function getStack(): StackInterface;

    /**
     * Get the URI to match for the next call.
     *
     * @return string
     */
    public function getUri(): string;

    /**
     * Get any matched variables
     *
     * @return array
     */
    public function getVariables(): array;

    /**
     * Set the URI to match against
     *
     * @param string $uri
     * @return ResultInterface
     */
    public function setUri(string $uri): ResultInterface;

    /**
     * Set the variables
     *
     * @param array $variables
     * @return ResultInterface
     */
    public function setVariables(array $variables): ResultInterface;
}