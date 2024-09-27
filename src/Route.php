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
     *
     * @vat string[]
     */
    const array ALLOWED_METHODS = [
        'DELETE', 'GET', 'HEAD', 'OPTIONS', 'PATCH', 'POST', 'PUT',
    ];

    /**
     * HTTP method/s for this route
     *
     * @var string[]
     */
    private array $method;

    /**
     * Route match
     *
     * @var string
     */
    private string $match;

    /**
     * @var callable|MiddlewareInterface
     */
    private $callback;

    /**
     * Middleware stack
     *
     * @var StackInterface
     */
    private StackInterface $stack;

    /**
     * Child routes
     *
     * @var Route[]
     */
    private array $routes = [];

    /**
     * @var callable|null
     */
    private $routesCallback;

    /**
     * Route constructor.
     *
     * @param string[]|string $method
     * @param string $match
     * @param array{string, string}|callable|MiddlewareInterface $callback
     * @param callable|null $routesCallback Add routes using a callback
     *
     * Callback should look like function (Route $route) { $route->addRoute(...); }
     */
    public function __construct(array|string $method, string $match, array|callable|MiddlewareInterface $callback, callable $routesCallback = null)
    {
        $this->stack = new Stack();
        $this->method = (is_array($method) ? $method : [$method]);
        $this->match = $match;
        $this->callback = $callback;
        $this->routesCallback = $routesCallback;
    }

    /**
     * Get the arguments for the call
     *
     * @param string $name
     * @param mixed[] $arguments
     * @return mixed[]
     */
    public static function getArgumentsForCall(string $name, array $arguments): array
    {
        // uppercase the method name
        $name = strtoupper($name);

        if ($name === 'ANY') {
            // allowed any of the methods
            $name = self::ALLOWED_METHODS;
        } elseif (! in_array($name, self::ALLOWED_METHODS)) {
            throw new InvalidArgumentException('Route::method must be one of: ' . implode(', ', self::ALLOWED_METHODS));
        }

        // add the name to the arguments
        array_unshift($arguments, $name);

        // return the arguments
        return $arguments;
    }

    /**
     * Create the route using Route::post($match, $callback, $routesCallback);
     *
     * @param string $name
     * @param mixed[] $arguments
     * @return RouteInterface
     * @throws \ReflectionException
     */
    public static function __callStatic(string $name, array $arguments): mixed
    {
        return (new \ReflectionClass(static::class))->newInstanceArgs(static::getArgumentsForCall($name, $arguments));
    }

    /**
     * Create the route using $route->post($match, $callback, $routesCallback);
     *
     * @param string $name
     * @param mixed[] $arguments
     * @return RouteInterface
     */
    public function __call(string $name, array $arguments): mixed
    {
        return call_user_func_array([$this, 'addRoute'], static::getArgumentsForCall($name, $arguments));
    }

    /**
     * @inheritDoc
     */
    public function addRoute($method, string $match, array|callable|MiddlewareInterface $callback, callable $routesCallback = null): RouteInterface
    {
        return $this->routes[] = new self($method, $match, $callback, $routesCallback);
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
    public function getCallback(): array|callable|MiddlewareInterface
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

        if ($result->match() === ResultInterface::MATCH_NONE) {
            // no match
            return $result;
        }

        if ($result->match() === ResultInterface::MATCH_FULL && in_array($method, $this->method)) {
            // valid match
            return (new Result(ResultInterface::MATCH_FULL, $this))
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
            if ($routeResult->match() === ResultInterface::MATCH_FULL) {
                // add this routes middleware stack to the result
                return $routeResult->addStack($this->stack);
            }
        }

        return new Result();
    }
}