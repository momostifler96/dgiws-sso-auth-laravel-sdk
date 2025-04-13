<?php

namespace Momoledev\DgiwsAuthLaravelSdk;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;

class AppServiceProvider extends ServiceProvider
{
    public function boot(Router $router)
    {
        $router->aliasMiddleware('dgiws.auth', SSOAuthenticateMiddleware::class);
    }
}
