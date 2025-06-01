<?php

return [
    'docker_container_name' => env('SSO_SERVER_CONTAINER_NAME', 'dgiws-auth'),
    'base_url' => env('SSO_SERVER_URL'),
    'login_url' => env('SSO_SERVER_LOGIN_URL', env('SSO_SERVER_URL')),
    'authorize_url' => env('SSO_SERVER_AUTHORIZE_URL', env('SSO_SERVER_URL') . '/oauth/authorize'),
    'token_url' => env('SSO_SERVER_TOKEN_URL', env('SSO_SERVER_URL') . '/oauth/token'),
    'user_info_path' => env('SSO_SERVER_USER_INFO_PATH', '/api/account/me'),
    'user_info_url' => env('SSO_SERVER_USER_INFO_URL', env('SSO_SERVER_URL') . env('SSO_SERVER_USER_INFO_PATH', '/api/account/me')),
    'client_id' => env('SSO_SERVER_CLIENT_ID', ''),
    'client_hosts_ids' => [],
    'client_secret' => env('SSO_SERVER_CLIENT_SECRET', null),
    'login_route' => [
        'path' => env('SSO_LOGIN_ROUTE_PATH', '/login'),
        'middlewares' => [],
        'name' => env('SSO_LOGIN_ROUTE_NAME', 'sso.login'),
    ],
    'callback_route' => [
        'path' => env('SSO_CALLBACK_ROUTE_PATH', '/auth/callback'),
        'middlewares' => [],
        'name' => env('SSO_CALLBACK_ROUTE_NAME', 'sso.callback'),
    ],
];
