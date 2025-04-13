<?php

namespace Momoledev\DgiwsAuthLaravelSdk\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Momoledev\DgiwsAuthLaravelSdk\Services\SSOService;

class SSOAuthController extends Controller
{

    public function redirectToSSO()
    {
        $provider = SSOService::getProvider();

        $code_verifier = bin2hex(random_bytes(64)); // générer une chaîne sécurisée
        $code_challenge = SSOService::base64url_encode(hash('sha256', $code_verifier, true));
        $authorizationUrl = $provider->getAuthorizationUrl([
            'code_challenge'         => $code_challenge, // pour PKCE
            'code_challenge_method'  => 'S256'
        ]);

        session([
            'oauth2CodeVerifier' => $code_verifier,
            'oauth2State' => $provider->getState()
        ]);

        return redirect($authorizationUrl);
    }

    public function handleCallback(Request $request)
    {
        $provider = SSOService::getProvider();

        if ($request->get('state') !== session('oauth2State')) {
            abort(403, 'Invalid state');
        }
        // dd($request->all());
        $accessToken = $provider->getAccessToken('authorization_code', [
            'code' => $request->get('code'),
            'code_verifier' => session('oauth2CodeVerifier') // si tu fais PKCE complet
        ]);

        $resourceOwner = $provider->getResourceOwner($accessToken);
        $user = $resourceOwner->toArray();
        // dd($user, $accessToken);
        // ici, tu peux logger ou créer l’utilisateur dans ta base locale
        session(['user' => $user]);
        session(['access_token' => $accessToken->getToken()]);
        session(['access_token_expires' => $accessToken->getExpires()]);

        return redirect('/');
    }
}
