<?php

use Illuminate\Support\Facades\Route;
use Momoledev\DgiwsAuthLaravelSdk\Controllers\SSOAuthController;

Route::middleware('dgiws.sso.guest')->group(function () {
    Route::get(config('sso.login_route.path', '/login'), [SSOAuthController::class, 'redirectToSSO'])->name(config('sso.login_route.name', 'sso.login'))->middleware(config('sso.login_route.middleware', []));
    Route::get(config('sso.callback_route.path', '/login/callback'), [SSOAuthController::class, 'handleCallback'])->name(config('sso.callback_route.name', 'sso.callback'))->middleware(config('sso.callback_route.middleware', []));
});
