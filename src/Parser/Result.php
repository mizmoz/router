<?php

namespace Mizmoz\Router\Parser;

use Mizmoz\Router\Contract\Parser\ResultInterface;
use Mizmoz\Router\Contract\RouteInterface;
use Mizmoz\Router\Contract\StackInterface;
use Mizmoz\Router\Stack;

class Result implements ResultInterface
{
    /**
     * @var string
     */
    private $match;

    /**
     * @var RouteInterface
     */
    private $route;

    /**
     * @var StackInterface
     */
    private $stack;

    /**
     * @var string
     */
    private $uri = '';

    /**
     * @var array
     */
    private $variables = [];

    /**
     * Result constructor.
     * @param string $match
     * @param RouteInterface $route
     */
    public function __construct(string $match = self::MATCH_NONE, RouteInterface $route = null)
    {
        $this->match = $match;
        $this->route = $route;
        $this->stack = new Stack();
    }

    /**
     * Add items to the stack
     *
     * @param StackInterface $stack
     * @return ResultInterface
     */
    public function addStack(StackInterface $stack): ResultInterface
    {
        $this->stack->addStack($stack, false);
        return $this;
    }

    /**
     * Get the match type
     *
     * @return string
     */
    public function match(): string
    {
        return $this->match;
    }

    /**
     * Get the matched route
     *
     * @return RouteInterface
     */
    public function getRoute(): RouteInterface
    {
        return $this->route;
    }

    /**
     * Get the stack
     *
     * @return StackInterface
     */
    public function getStack(): StackInterface
    {
        return $this->stack;
    }

    /**
     * Get the URI to match for the next call.
     *
     * @return string
     */
    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * Get any matched variables
     *
     * @return array
     */
    public function getVariables(): array
    {
        return $this->variables;
    }

    /**
     * Set the URI to match against
     *
     * @param string $uri
     * @return ResultInterface
     */
    public function setUri(string $uri): ResultInterface
    {
        $this->uri = $uri;
        return $this;
    }

    /**
     * Set the variables
     *
     * @param array $variables
     * @return ResultInterface
     */
    public function setVariables(array $variables): ResultInterface
    {
        $this->variables = $variables;
        return $this;
    }
}