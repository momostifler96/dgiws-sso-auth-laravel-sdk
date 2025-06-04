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
    'user_model' => env('SSO_USER_MODEL', null),
    'server_logout_url' => env('SSO_SERVER_LOGOUT_URL'),
    'login_route' => [
        'path' => env('SSO_LOGIN_ROUTE_PATH', '/login'),
        'middlewares' => ['dgiws.sso.guest'],
        'name' => env('SSO_LOGIN_ROUTE_NAME', 'sso.login'),
    ],
    'logout_route' => [
        'path' => env('SSO_LOGOUT_ROUTE_PATH', '/logout'),
        'middlewares' => ['dgiws.sso.auth'],
        'name' => env('SSO_LOGOUT_ROUTE_NAME', 'sso.logout'),
    ],
    'silent_logout' => [
        'path' => env('SSO_SILENT_LOGOUT_ROUTE_PATH', '/oauth/silent-logout'),
        'middlewares' => ['dgiws.sso.auth'],
        'name' => env('SSO_SILENT_LOGOUT_ROUTE_NAME', 'sso.silent-logout'),
    ],
    'callback_route' => [
        'path' => env('SSO_CALLBACK_ROUTE_PATH', '/auth/callback'),
        'middlewares' => [],
        'name' => env('SSO_CALLBACK_ROUTE_NAME', 'sso.callback'),
    ],
];
