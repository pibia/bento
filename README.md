# Bento mini framework

> Just a simple PHP based framework

## Routing

Create first simple route with uri /first-route and controller `Home` with method `first` for all methods

```php
$router->add('/first-route', 'v1\Home::first');
```

Create route with different methods

```php
$router->get('/route-method', 'v1\Home::get');
$router->post('/route-method', 'v1\Home::post');
$router->put('/route-method', 'v1\Home::put');
$router->patch('/route-method', 'v1\Home::patch');
$router->delete('/route-method', 'v1\Home::delete');
$router->options('/route-method', 'v1\Home::options');
$router->map('GET|POST', '/route-method', 'v1\Home::getOrPost');
$router->map('PUT|PATCH|DELETE', '/route-method', 'v1\Home::putOrPatchOrDelete');
```

Declaring route is available for callable anonymoys function, declaring string or array

> Anonymous callable function
```php
$router->add('/callable', function () {
	echo 'You called anonymous function';
});
```

> Declaring with string
```php
$router->get('/route-string', 'v1\v1\Home::string');
```

> Declaring with array
```php
$router->get('/route-array', [v1\Index::class, 'array']);
```