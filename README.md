# [Mizmoz](https://www.mizmoz.com) / Router

A Simple Router for HTTP

## Aims

- Be lightweight
- Use PSR-7 HTTP message interfaces
- Use PSR-15 HTTP server middleware (when it becomes standardised)
- Resolution with PSR-11: Container interface

## Basic Usage

```php
// create a simple route
$route = Route::get('/', function (RequestInterface $request, ResponseInterface $response, ResultInterface $result) {
  return $response;
});

// init with a PSR-11 compatible container for class resolution
$dispatcher = new Router($route, $container);

// dispatch the route by passing a PSR-7 compatible Request
$response = $dispatcher->dispatch(new Request('GET', '/'));

exit($response->getBody()->getContents());

```

#### More examples

```php
// class callback which will call the default process($request, $response, $next);
Route::get('/admin', AdminController::class);

// class callback with specific method
Route::get('/admin', [AdminController::class, 'home']);

// Route to app\actions\User\GetProfile.php
Route::get('/profile/{:userId}', 'User\GetProfile');

// A more complete route
Route::get('/', HomePage::class, function (RouteInterface $r) {
    // add some child routes
    $r->addRoute('GET', '/profile', ...);
});
```

#### Routes with variables

```php
// create the route with simple variable matching using :variable format
$route = new Route('GET', '/users/:userId', UserGet::class);

// get the variable
$userId = $route->match('GET', '/users/123')->getVariable('userId');
```

#### Middleware

```php
// add global middleware
$router->addMiddleware(new EnsureSsl());

// add middlware to a route, middleware is added before the route definition by default
$router->get('admin', AdminController::class)
    ->addMiddleware(new EnsureSsl());

// add to all routes
$router->get('/', function (RouteInterface $r) {
    $r->get('/users', ...);
})->addMiddleware(new AclUser('admin'));
```

## Roadmap

- Add REST API helper for classes