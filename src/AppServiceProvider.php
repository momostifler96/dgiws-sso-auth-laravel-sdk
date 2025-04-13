<?php

namespace Momoledev\DgiwsAuthLaravelSdk;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use Momoledev\DgiwsAuthLaravelSdk\Middlewares\SSOAuthenticateGuestMiddleware;
use Momoledev\DgiwsAuthLaravelSdk\Middlewares\SSOAuthenticateMiddleware;

class AppServiceProvider extends ServiceProvider
{
    public function boot(Router $router)
    {
        $router->aliasMiddleware('dgiws.sso.auth', SSOAuthenticateMiddleware::class);
        $router->aliasMiddleware('dgiws.sso.guest', SSOAuthenticateGuestMiddleware::class);
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        $this->mergeConfigFrom(
            __DIR__ . '/../config/sso.pph',
            'sso'
        );
        $this->publishes([
            __DIR__ . '/../config/sso.php' => config_path('sso.php'),
        ], 'dgiws-auth-laravel-sdk-config');
    }
}
