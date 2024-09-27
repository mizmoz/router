<?php

namespace Mizmoz\Router\Tests;

use GuzzleHttp\Psr7\ServerRequest;
use GuzzleHttp\Psr7\Utils;
use Mizmoz\Router\Contract\RouteInterface;
use Mizmoz\Router\Dispatcher;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

trait CreateCallbackTrait
{
    /**
     * Make an OK response callback
     *
     * @param null $body
     * @return \Closure
     */
    private function callbackWithOkResponse($body = null): \Closure
    {
        return function (RequestInterface $request, ResponseInterface $response) use ($body) {
            return is_null($body) ? $response : $response->withBody(Utils::streamFor($body));
        };
    }

    /**
     * Dispatch and get the response
     *
     * @param RouteInterface $route
     * @param string $uri
     * @param string $method
     * @return ResponseInterface
     */
    private function getResponse(RouteInterface $route, string $uri, string $method = 'GET'): ResponseInterface
    {
        return (new Dispatcher($route, new Container()))
            ->dispatch(new ServerRequest($method, 'https://www.mizmoz.com' . $uri));
    }

    /**
     * Get the contents from the
     *
     * @param RouteInterface $route
     * @param string $uri
     * @param string $method
     * @return string|null
     */
    private function getContents(RouteInterface $route, string $uri, string $method = 'GET'): ?string
    {
        return $this->getResponse($route, $uri, $method)
            ->getBody()
            ->getContents();
    }
}