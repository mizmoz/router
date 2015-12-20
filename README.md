# router
Router for HTTP and CLI

## Basic Usage
```php
$router = new Router();

$router->get('/dashboad', function () {
  exit('Dashboard');
});

// Set the Mizmoz Controller adapter for name resolution
$router->setResolver(new Router\Adapter\Mizmoz);

// Route to app\actions\User\GetProfile.php
$router->get('/profile/:userId', 'User\GetProfile');

$router->route('https://www.mizmoz.com/profile/123', 'GET');
```
