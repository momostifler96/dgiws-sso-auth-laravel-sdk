<?php

use Illuminate\Support\Facades\Route;
use Momoledev\DgiwsAuthLaravelSdk\Controllers\SSOAuthController;

Route::middleware(['web', 'dgiws.sso.guest'])->group(function () {
    Route::get(config('sso.login_route.path', '/login'), [SSOAuthController::class, 'redirectToSSO'])->name(config(key: 'sso.login_route.name'))->middleware(config('sso.login_route.middleware', []));
    Route::get(config('sso.callback_route.path', '/login/callback'), [SSOAuthController::class, 'handleCallback'])->name(config('sso.callback_route.name'))->middleware(config('sso.callback_route.middleware', []));
});

Route::middleware(['web', 'dgiws.sso.auth'])->group(function () {
    Route::post(config('sso.logout_route.path', 'oauth/logout'), [SSOAuthController::class, 'logout'])->name(config(key: 'sso.logout_route.name'))->middleware(config('sso.logout_route.middleware', []));
    Route::get(config('sso.silent_logout.path', 'oauth/silent-logout'), [SSOAuthController::class, 'silentLogout'])->name(config(key: 'sso.silent_logout.name'))->middleware(config('sso.silent_logout.middleware', []));
});
