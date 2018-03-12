<?php

namespace Mizmoz\Router\Tests;

use GuzzleHttp\Psr7\ServerRequest;
use Mizmoz\Router\Contract\RouteInterface;
use Mizmoz\Router\Dispatcher;
use Mizmoz\Router\Route;
use Psr\Http\Message\ResponseInterface;

class DispatcherTest extends TestCase
{
    use CreateCallbackTrait;

    public function testDispatchRoute()
    {
        // create test route
        $route = new Route('GET', '/', $this->callbackWithOkResponse('Homepage'));

        // create the dispatcher
        $response = (new Dispatcher($route))->dispatch(new ServerRequest('GET', 'https://www.mizmoz.com/'));

        // check we get a response
        $this->assertInstanceOf(ResponseInterface::class, $response);

        // check it's OK
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testDispatchRouteWithMiddleware()
    {
        // create the route with middleware
        $route = (new Route('GET', '/', $this->callbackWithOkResponse('Homepage')))
            ->addMiddleware(new SetResponseMiddleware('X-Middleware', 'Middleware-1'));

        // create the dispatcher
        $response = (new Dispatcher($route))->dispatch(new ServerRequest('GET', 'https://www.mizmoz.com/'));

        // check it's OK
        $this->assertEquals(200, $response->getStatusCode());

        // check we get the correct response
        $this->assertEquals(['Middleware-1'], $response->getHeader('X-Middleware'));
    }

    public function testDispatchRouteWithInheritedMiddleware()
    {
        // create the route with middleware
        $route = (new Route('GET', '/', $this->callbackWithOkResponse('Homepage'), function (RouteInterface $r) {
            $r->addRoute('GET', '/dashboard', $this->callbackWithOkResponse('Dashboard'));
        }))->addMiddleware(new SetResponseMiddleware('X-Middleware', 'Outer'));

        // create the dispatcher
        $response = (new Dispatcher($route))->dispatch(new ServerRequest('GET', 'https://www.mizmoz.com/dashboard'));

        // check it's OK
        $this->assertEquals(200, $response->getStatusCode());

        // check we get the correct response
        $this->assertEquals(['Outer'], $response->getHeader('X-Middleware'));
    }

    public function testDispatchRouteWithInheritedAndOwnMiddleware()
    {
        // create the route with middleware
        $route = (new Route('GET', '/', $this->callbackWithOkResponse('Homepage'), function (RouteInterface $r) {
            $r->addRoute('GET', '/dashboard', $this->callbackWithOkResponse('Dashboard'))
                ->addMiddleware(new SetResponseMiddleware('X-Middleware', 'Inner'));
        }))->addMiddleware(new SetResponseMiddleware('X-Middleware', 'Outer'));

        // create the dispatcher
        $response = (new Dispatcher($route))->dispatch(new ServerRequest('GET', 'https://www.mizmoz.com/dashboard'));

        // check it's OK
        $this->assertEquals(200, $response->getStatusCode());

        // check we get the correct response
        $this->assertEquals(['Outer', 'Inner'], $response->getHeader('X-Middleware'));
    }
}