<?php

namespace Momoledev\DgiwsAuthLaravelSdk\Services;

use League\OAuth2\Client\Provider\GenericProvider;

class SSOService
{
    private static $provider;
    public static function getProvider()
    {
        if (!self::$provider) {
            self::$provider = new GenericProvider([
                'clientId'                => config('sso.client_id'),
                'redirectUri'             => route('sso.callback_route.path'),
                'urlAuthorize'            => config('sso.authorize_url'),
                'urlAccessToken'          => config('sso.token_url'),
                'urlResourceOwnerDetails' => config('sso.docker_container_name') . config('sso.user_info_path'),
            ]);
        }
        return self::$provider;
    }
    public static function base64url_encode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
