<?php

namespace Mizmoz\Router\Contract;

use Mizmoz\Router\Contract\Parser\ResultInterface;

/**
 * Interface RouteInterface
 * @package Mizmoz\Router\Contract
 *
 * @method static RouteInterface delete($match, $callback, callable $routesCallback = null);
 * @method static RouteInterface get($match, $callback, callable $routesCallback = null);
 * @method static RouteInterface head($match, $callback, callable $routesCallback = null);
 * @method static RouteInterface options($match, $callback, callable $routesCallback = null);
 * @method static RouteInterface patch($match, $callback, callable $routesCallback = null);
 * @method static RouteInterface post($match, $callback, callable $routesCallback = null);
 * @method static RouteInterface put($match, $callback, callable $routesCallback = null);
 * @method static RouteInterface any($match, $callback, callable $routesCallback = null);
 */
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
