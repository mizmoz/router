<?php

namespace Mizmoz\Router\Contract;

use Mizmoz\Router\Contract\Parser\ResultInterface;

interface RouteInterface
{
    /**
     * Add a route to the router
     *
     * @param string|array $method
     * @param string $match
     * @param callable|MiddlewareInterface $callback
     * @param callable $routesCallback
     * @return RouteInterface
     */
    public function addRoute($method, string $match, $callback, callable $routesCallback = null): RouteInterface;

    /**
     * Add middleware to the route
     *
     * @param MiddlewareInterface $middleware
     * @return RouteInterface
     */
    public function addMiddleware(MiddlewareInterface $middleware): RouteInterface;

    /**
     * Get the callback
     *
     * @return callable|MiddlewareInterface
     */
    public function getCallback();

    /**
     * Get the request stack
     *
     * @return StackInterface
     */
    public function getStack(): StackInterface;

    /**
     * Match the route
     *
     * @param string $method
     * @param string $uri
     * @return ResultInterface
     */
    public function match(string $method, string $uri): ResultInterface;
}
