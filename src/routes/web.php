<?php

use Illuminate\Support\Facades\Route;
use Momoledev\DgiwsAuthLaravelSdk\Controllers\SSOAuthController;

Route::middleware(['web', 'dgiws.sso.guest'])->group(function () {
    Route::get(config('sso.login_route.path', '/login'), [SSOAuthController::class, 'redirectToSSO'])->name(config(key: 'sso.login_route.name'))->middleware(config('sso.login_route.middleware', []));
    Route::get(config('sso.callback_route.path', '/login/callback'), [SSOAuthController::class, 'handleCallback'])->name(config('sso.callback_route.name'))->middleware(config('sso.callback_route.middleware', []));
});
