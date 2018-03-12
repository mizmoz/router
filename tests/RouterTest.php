<?php

namespace Mizmoz\Router\Tests;

use Mizmoz\Router\Contract\RouteInterface;
use Mizmoz\Router\Route;

class RouterTest extends TestCase
{
    use CreateCallbackTrait;

    /**
     * Test some plain text routing
     */
    public function testPlainRoute()
    {
        // create the root route
        $route = new Route('GET', '/', $this->callbackWithOkResponse('Homepage'));

        // match the route
        $this->assertEquals('Homepage', $this->getContents($route, '/'));
    }

    /**
     * Test some variable routing
     */
    public function testVariableRoute()
    {
        // create the root route
        $route = new Route('GET', '/user/:userId', $this->callbackWithOkResponse('User'));

        // match the route
        $this->assertEquals('User', $this->getContents($route, '/user/123'));
    }

    /**
     * Test some variable routing
     */
    public function testPlainRouteWithClassResolution()
    {
        // create the root route
        $route = new Route('GET', '/user/:userId', [CallbackClass::class, 'callThis']);

        // match the route
        $this->assertEquals(CallbackClass::RESPONSE, $this->getContents($route, '/user/123'));
    }

    /**
     * Test some basic routing
     */
    public function testMultipleBasicRoutes()
    {
        // create the root route
        $route = new Route('GET', '/', $this->callbackWithOkResponse('Homepage'), function (RouteInterface $root) {
            // add some children
            $root->addRoute('GET', '/about-us', $this->callbackWithOkResponse('About Us'));
            $root->addRoute('POST', '/register', $this->callbackWithOkResponse('Register'));

            // add dashboard with children
            $root->addRoute('GET', '/dashboard', $this->callbackWithOkResponse('Dashboard'), function (RouteInterface $dashboard) {
                $dashboard->addRoute('GET', '/reports', $this->callbackWithOkResponse('Dashboard Reports'));
            });
        });

        // check the routes are accessible
        $this->assertEquals('Homepage', $this->getContents($route, '/'));
        $this->assertEquals('About Us', $this->getContents($route, '/about-us'));
        $this->assertEquals('Register', $this->getContents($route, '/register', 'POST'));
        $this->assertEquals('Dashboard', $this->getContents($route, '/dashboard'));
        $this->assertEquals('Dashboard Reports', $this->getContents($route, '/dashboard/reports'));
    }

    /**
     * Test adding routes with the magic methods
     */
    public function testMultipleRoutesUsingMagicMethods()
    {
        $route = Route::get('/', $this->callbackWithOkResponse('Homepage'), function (RouteInterface $root) {
            $root->get('/app', $this->callbackWithOkResponse('App'));
        });

        // check the routes are accessible
        $this->assertEquals('Homepage', $this->getContents($route, '/'));
        $this->assertEquals('App', $this->getContents($route, '/app'));
    }

    /**
     * Test catching all routes
     */
    public function testRouteCatchAll()
    {
        $route = Route::get('/', $this->callbackWithOkResponse('Homepage'), function (RouteInterface $root) {
            // some app route
            $root->get('/app', $this->callbackWithOkResponse('App'));

            // add catch all for all missing routes
            $root->get('/*', $this->callbackWithOkResponse('Error'));
        });

        // check the routes are accessible
        $this->assertEquals('Homepage', $this->getContents($route, '/'));
        $this->assertEquals('Error', $this->getContents($route, '/app/cheese'));
    }
}