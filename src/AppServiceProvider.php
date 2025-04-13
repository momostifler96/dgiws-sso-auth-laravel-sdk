<?php

namespace Momoledev\DgiwsAuthLaravelSdk;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;

class AppServiceProvider extends ServiceProvider
{
    public function boot(Router $router)
    {
        $router->aliasMiddleware('dgiws.sso.auth', SSOAuthenticateMiddleware::class);
        $router->aliasMiddleware('dgiws.sso.guest', SSOAuthenticateGuestMiddleware::class);
    }
}
