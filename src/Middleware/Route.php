<?php

namespace Mizmoz\Router\Middleware;

use Mizmoz\Router\Contract\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class Route
 * @package Mizmoz\Router\Middleware
 *
 * Wraps a callback or class method in a route handling middleware.
 * This will take take of building the response object from the returned data.
 */
class Route implements MiddlewareInterface
{
    /**
     * @var callable
     */
    private $callback;

    /**
     * Route constructor.
     * @param callable $callback
     */
    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * @inheritDoc
     */
    public function process(
        ServerRequestInterface $request,
        ResponseInterface $response,
        MiddlewareInterface $next = null
    ): ResponseInterface
    {
        // execute the callback
        $callback = $this->callback;
        $result = $callback($request, $response);

        // handle the result
        if ($result instanceof ResponseInterface) {
            $response = $result;
        }

        return ($next ? $next->process($request, $response) : $response);
    }
}