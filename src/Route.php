<?php

namespace Mizmoz\Router;

use Mizmoz\Router\Contract\MiddlewareInterface;
use Mizmoz\Router\Contract\Parser\ResultInterface;
use Mizmoz\Router\Contract\RouteInterface;
use Mizmoz\Router\Contract\StackInterface;
use Mizmoz\Router\Exception\InvalidArgumentException;
use Mizmoz\Router\Parser\Factory;
use Mizmoz\Router\Parser\Result;

/**
 * Class Route
 * @package Mizmoz\Router
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
class Route implements RouteInterface
{
    /**
     * Allowed http methods
     */
    const ALLOWED_METHODS = [
        'DELETE', 'GET', 'HEAD', 'OPTIONS', 'PATCH', 'POST', 'PUT',
    ];

    /**
     * HTTP method/s for this route
     *
     * @var array
     */
    private $method;

    /**
     * Route match
     *
     * @var string
     */
    private $match;

    /**
     * @var callable|MiddlewareInterface
     */
    private $callback;

    /**
     * Middleware stack
     *
     * @var StackInterface
     */
    private $stack;

    /**
     * Child routes
     *
     * @var array
     */
    private $routes = [];

    /**
     * @var callable
     */
    private $routesCallback;

    /**
     * Route constructor.
     *
     * @param string|array $method
     * @param string $match
     * @param callable|MiddlewareInterface $callback
     * @param callable $routesCallback Add routes using a callback
     *
     * Callback should look like function (Route $route) { $route->addRoute(...); }
     */
    public function __construct($method, $match, $callback, callable $routesCallback = null)
    {
        $this->stack = new Stack();
        $this->method = (is_array($method) ? $method : [$method]);
        $this->match = $match;
        $this->callback = $callback;
        $this->routesCallback = $routesCallback;
    }

    /**
     * Create the route using Route::post($match, $callback, $routesCallback);
     *
     * @param $name
     * @param $arguments
     * @return RouteInterface
     */
    public static function __callStatic($name, $arguments)
    {
        // uppercase the method name
        $name = strtoupper($name);

        if ($name === 'ANY') {
            // allowed any of the methods
            $name = self::ALLOWED_METHODS;
        } else if (! in_array($name, self::ALLOWED_METHODS)) {
            throw new InvalidArgumentException('Route::method must be one of: ' . implode(', ', self::ALLOWED_METHODS));
        }

        // add the name to the arguments
        array_unshift($arguments, $name);

        // create and return the new route
        return (new \ReflectionClass(static::class))->newInstanceArgs($arguments);
    }

    /**
     * @inheritDoc
     */
    public function addRoute($method, string $match, $callback, callable $routesCallback = null): RouteInterface
    {
        return $this->routes[] = new static($method, $match, $callback, $routesCallback);
    }

    /**
     * @inheritDoc
     */
    public function addMiddleware(MiddlewareInterface $middleware): RouteInterface
    {
        $this->stack->addMiddleware($middleware);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * @inheritDoc
     */
    public function getStack(): StackInterface
    {
        return $this->stack;
    }

    /**
     * @inheritDoc
     */
    public function match(string $method, string $uri): ResultInterface
    {
        $result = Factory::getParser($this->match)->match($uri);

        if ($result->match() === Result::MATCH_NONE) {
            // no match
            return $result;
        }

        if ($result->match() === Result::MATCH_FULL && in_array($method, $this->method)) {
            // valid match
            return (new Result(Result::MATCH_FULL, $this))
                ->addStack($this->stack)
                ->setVariables($result->getVariables());
        }

        // partial match, try and load any extra routes
        if ($this->routesCallback) {
            $callback = $this->routesCallback;
            $callback($this);
        }

        /** @var RouteInterface $route */
        foreach ($this->routes as $route) {
            $routeResult = $route->match($method, $result->getUri());
            if ($routeResult->match() === Result::MATCH_FULL) {
                // add this routes middleware stack to the result
                return $routeResult->addStack($this->stack);
            }
        }

        return new Result();
    }
}