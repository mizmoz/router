<?php

namespace Mizmoz\Router;

use GuzzleHttp\Psr7\Response;
use Mizmoz\Router\Contract\DispatcherInterface;
use Mizmoz\Router\Contract\MiddlewareInterface;
use Mizmoz\Router\Contract\Parser\ResultInterface;
use Mizmoz\Router\Contract\RouteInterface;
use Mizmoz\Router\Exception\CannotExecuteRouteException;
use Mizmoz\Router\Parser\Result;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Dispatcher implements DispatcherInterface
{
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
    public function dispatch(RequestInterface $request): ResponseInterface
    {
        $result = $this->route->match($request->getMethod(), $request->getUri());

        if (in_array($result->match(), [Result::MATCH_PARTIAL, Result::MATCH_NONE])) {
            // failed to match
            return $this->responseNotFound();
        }

        // we found the route, first execute the middleware
        $response = $result->getStack()->process($request, new Response());

        // execute the main route
        return $this->executeRoute($request, $response, $result);
    }

    /**
     * Execute the route
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param ResultInterface $result
     * @return ResponseInterface
     */
    private function executeRoute(
        RequestInterface $request, ResponseInterface $response, ResultInterface $result
    ): ResponseInterface
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
            $callback = ($this->container ? function ($request, $response, $result) use ($class, $method) {
                $class = $this->container->get($class);
                return $class->$method($request, $response, $result);
            } : null);
        }

        if ($callback instanceof MiddlewareInterface) {
            return $callback->process($request, $response, $result);
        }

        if (is_callable($callback)) {
            return $callback($request, $response, $result);
        }

        throw new CannotExecuteRouteException('Dispatch doesn\'t know how to handle the callback');
    }

    /**
     * No route to handle this request
     *
     * @return ResponseInterface
     */
    private function responseNotFound(): ResponseInterface
    {
        return new Response(404);
    }
}