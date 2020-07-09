<?php

use Monolog\Logger;

require_once __DIR__.'/../vendor/autoload.php';

if (!function_exists('config_path')) {
    function config_path($path = '')
    {
        return app()->basePath() . '/config' . ($path ? '/' . $path : $path);
    }
}

try {
    (new Dotenv\Dotenv(__DIR__.'/../'))->load();
} catch (Dotenv\Exception\InvalidPathException $e) {
    //
}

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Here we will load the environment and create the application instance
| that serves as the central piece of this framework. We'll use this
| application as an "IoC" container and router for this framework.
|
*/

$app = new Laravel\Lumen\Application(
    realpath(__DIR__.'/../')
);

$app->withFacades();
$app->withEloquent();

/*
|--------------------------------------------------------------------------
| Register Container Bindings
|--------------------------------------------------------------------------
|
| Now we will register a few bindings in the service container. We will
| register the exception handler and the console kernel. You may add
| your own bindings here if you like or you can make another file.
|
*/

$app->alias('cache', 'Illuminate\Cache\CacheManager');

$app->singleton(Illuminate\Auth\AuthManager::class, function ($app) {
    return $app->make('auth');
});

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->configureMonologUsing(function (Monolog\Logger $monolog) use ($app) {
    $uniqid = uniqid();
    $monolog->pushProcessor(function ($record) use ($uniqid) {
        $record['message'] = '[logId:' . $uniqid . '] ' . $record['message'];

        return $record;
    });
    return $monolog->pushHandler(
        new \Monolog\Handler\RotatingFileHandler(storage_path() . '/logs/api.log', 0, Logger::DEBUG)
    );
});

/*
|--------------------------------------------------------------------------
| Register Middleware
|--------------------------------------------------------------------------
|
| Next, we will register the middleware with the application. These can
| be global middleware that run before and after each request into a
| route or middleware that'll be assigned to some specific routes.
|
*/

// $app->middleware([
//    App\Http\Middleware\ExampleMiddleware::class
// ]);

$app->routeMiddleware([
    'Shadow' => App\Http\Middleware\ShadowMiddleware::class
]);

if(!class_exists('JWTAuth')){
    class_alias('Tymon\JWTAuth\Facades\JWTAuth', 'JWTAuth');
}
if(!class_exists('JWTFactory')){
    class_alias('Tymon\JWTAuth\Facades\JWTFactory', 'JWTFactory');
}
/*
|--------------------------------------------------------------------------
| Register Service Providers
|--------------------------------------------------------------------------
|
| Here we will register all of the application's service providers which
| are used to bind services into the container. Service providers are
| totally optional, so you are not required to uncomment this line.
|
*/

$app->register(Illuminate\Redis\RedisServiceProvider::class);
$app->register(Tymon\JWTAuth\Providers\JWTAuthServiceProvider::class);
// $app->register(App\Providers\AppServiceProvider::class);
// $app->register(App\Providers\AuthServiceProvider::class);
// $app->register(App\Providers\EventServiceProvider::class);

/*
|--------------------------------------------------------------------------
| Load The Application Routes
|--------------------------------------------------------------------------
|
| Next we will include the routes file so that they can all be added to
| the application. This will provide all of the URLs the application
| can respond to, as well as the controllers that may handle them.
|
*/

$app->router->group([
    'namespace' => 'App\Http\Controllers',
], function ($router) {
    require __DIR__.'/../routes/api.php';
});

app()->configure('jwt');

return $app;
