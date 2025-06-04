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

        $code_verifier = bin2hex(random_bytes(64));
        $code_challenge = SSOService::base64url_encode(hash('sha256', $code_verifier, true));
        $authorizationUrl = $provider->getAuthorizationUrl([
            'code_challenge'         => $code_challenge,
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
        $accessToken = $provider->getAccessToken('authorization_code', [
            'code' => $request->get('code'),
            'code_verifier' => session('oauth2CodeVerifier')
        ]);

        $resourceOwner = $provider->getResourceOwner($accessToken);
        $user = $resourceOwner->toArray();

        session()->forget(['oauth2CodeVerifier', 'oauth2State']);

        session(['user' => $user]);
        session(['access_token' => $accessToken->getToken()]);
        session(['access_token_expires' => $accessToken->getExpires()]);

        return redirect('/');
    }

    public function logout(Request $request)
    {
        // Invalider la session locale de l'application
        // Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Rediriger vers le point de terminaison de déconnexion du serveur d'authentification
        // Le serveur d'authentification gérera la déconnexion de tous les clients
        return redirect(config('sso.server_logout_url'));
    }

    public function silentLogout()
    {
        session()->forget(['user', 'access_token', 'access_token_expires']);
        session()->forget(['oauth2CodeVerifier', 'oauth2State']);
        session()->invalidate();
        session()->regenerate();
        return redirect('/');
    }
}
