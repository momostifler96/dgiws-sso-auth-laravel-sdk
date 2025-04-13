<?php

namespace Momoledev\DgiwsAuthLaravelSdk;

use League\OAuth2\Client\Provider\GenericProvider;

class SSOService
{
    private static $provider;
    public static function getProvider()
    {
        if (!self::$provider) {
            self::$provider = new GenericProvider([
                'clientId'                => env('SSO_SERVER_CLIENT_ID'),
                'redirectUri'             => route('sso.callback'),
                'urlAuthorize'            => env('SSO_SERVER_AUTHORIZE_URL'),
                'urlAccessToken'          => env('SSO_SERVER_TOKEN_URL'),
                'urlResourceOwnerDetails' => env('SSO_SERVER_USER_INFO_URL'),
            ]);
        }
        return self::$provider;
    }
    public static function base64url_encode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
