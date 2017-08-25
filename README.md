# [Mizmoz](https://www.mizmoz.com) / Router

Router for HTTP and CLI

## Aims

- Be lightweight
- Use PSR-7 HTTP message interfaces
- Use PSR-15 HTTP server middleware (when it becomes standardised)
- Resolution with PSR-11: Container interface

## Basic Usage

```php
// init with a PSR-11 compatible container for class resolution
$router = new Router($container);

// simple callback
$router->get('/dashboard', function (RequestInterface $request, ResponseInterface $response, callable $next) {
  ...
});

// class callback which will call the default process($request, $response, $next);
$router->get('/admin', AdminController::class);

// class callback with specific method
$router->get('/admin', [AdminController::class, 'home']);

// Route to app\actions\User\GetProfile.php
$router->get('/profile/{:userId}', 'User\GetProfile');

$router->route('https://www.mizmoz.com/profile/123', 'GET');
```

#### Middleware

```php
// add global middleware
$router->addMiddleware(new EnsureSsl());

// add regex matched middleware
$router->addMiddleware(new EnsureSsl(), '^/admin');

// add middlware to a route, middleware is added before the route definition by default
$router->get('admin', AdminController::class)
    ->addMiddleware(new EnsureSsl());

// add middleware after the route
$router->get('admin', AdminController::class)
    ->addMiddleware(new LogAction(), Middleware::AFTER_ROUTE);

// add to groups
$router->group('/admin', function (RouteInterface $r) {
    $r->get('/users', ...);
})->addMiddleware(new AclUser('admin'));
```
