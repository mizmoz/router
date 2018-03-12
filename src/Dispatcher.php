<?php

namespace Mizmoz\Router;

use GuzzleHttp\Psr7\Response;
use Mizmoz\Router\Contract\DispatcherInterface;
use Mizmoz\Router\Contract\MiddlewareInterface;
use Mizmoz\Router\Contract\Parser\ResultInterface;
use Mizmoz\Router\Contract\RouteInterface;
use Mizmoz\Router\Exception\CannotExecuteRouteException;
use Mizmoz\Router\Exception\RouteNotFoundException;
use Mizmoz\Router\Middleware\Route;
use Mizmoz\Router\Parser\Result;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Dispatcher implements DispatcherInterface
{
    const ATTRIBUTE_RESULT_KEY = 'routeResult';

    /**
     * @var RouteInterface
     */
    private $route;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Dispatcher constructor.
     *
     * @param RouteInterface $route
     * @param ContainerInterface $container
     */
    public function __construct(RouteInterface $route, ContainerInterface $container = null)
    {
        $this->route = $route;
        $this->container = $container;
    }

    /**
     * @inheritDoc
     */
    public function dispatch(ServerRequestInterface $request): ResponseInterface
    {
        $result = $this->route->match($request->getMethod(), $request->getUri()->getPath());

        if (in_array($result->match(), [Result::MATCH_PARTIAL, Result::MATCH_NONE])) {
            // failed to match the route
            throw (new RouteNotFoundException())->setRequest($request);
        }

        // add the route variables to the request
        $request = $request->withAttribute(self::ATTRIBUTE_RESULT_KEY, $result);

        // we found the route, first execute the middleware
        return $result->getStack()
            ->addMiddleware($this->getRouteAsMiddleware($result))
            ->process($request, new Response());
    }

    /**
     * Get the route as a middleware
     *
     * @param ResultInterface $result
     * @return MiddlewareInterface
     */
    private function getRouteAsMiddleware(ResultInterface $result): MiddlewareInterface
    {
        $callback = $result->getRoute()->getCallback();

        // resolve the callback
        if (is_string($callback)) {
            // resolve using the container
            $callback = ($this->container ? $this->container->get($callback) : null);
        }

        if (is_array($callback) && count($callback) === 2) {
            // resolve the [class, method] call
            list($class, $method) = $callback;
            $callback = ($this->container ? function ($request, $response) use ($class, $method) {
                $class = $this->container->get($class);
                return $class->$method($request, $response);
            } : null);
        }

        if ($callback instanceof MiddlewareInterface) {
            // return the middleware
            return $callback;
        }

        if (is_callable($callback)) {
            return new Route($callback);
        }

        throw new CannotExecuteRouteException('Dispatch doesn\'t know how to handle the callback');
    }
}