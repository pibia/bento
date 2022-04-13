# Bento mini framework

> Just a simple PHP based framework

## Routing

Create first simple route with uri /first-route and controller `Home` with method `first` for all methods

```php
$router->add('/first-route', 'v1\Homefirst');
```

Create route with different methods

```php
$router->get('/route-method', 'v1\Homeget');
$router->post('/route-method', 'v1\Homepost');
$router->put('/route-method', 'v1\Homeput');
$router->patch('/route-method', 'v1\Homepatch');
$router->delete('/route-method', 'v1\Homedelete');
$router->options('/route-method', 'v1\Homeoptions');
$router->map('GET|POST', '/route-method', 'v1\HomegetOrPost');
$router->map('PUT|PATCH|DELETE', '/route-method', 'v1\HomeputOrPatchOrDelete');
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
$router->get('/route-string', 'v1\v1\Homestring');
```

> Declaring with array
```php
$router->get('/route-array', [v1\Index::class, 'array']);
```