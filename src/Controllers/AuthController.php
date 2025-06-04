<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // Assurez-vous que votre modèle User existe
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as AuthUser;
use Illuminate\Routing\Controller;

class AuthServerController extends Controller
{
    public function redirectToAuthServer()
    {
        $query = http_build_query([
            'client_id' => config('services.auth_server.client_id'),
            'redirect_uri' => config('services.auth_server.redirect'),
            'response_type' => 'code',
            'scope' => 'openid profile email', // Demandez les scopes nécessaires
            'state' => csrf_token(), // Protection CSRF
        ]);

        session(['state' => csrf_token()]); // Stockez le state pour la vérification

        return redirect(config('services.auth_server.authorize_url') . '?' . $query);
    }

    public function handleAuthServerCallback(Request $request)
    {
        $state = $request->input('state');
        if ($state !== session()->pull('state')) { // Vérifiez le state pour la protection CSRF
            // Gérer l'erreur CSRF
            return redirect('/')->with('error', 'Invalid state parameter.');
        }

        $response = Http::post(config('services.auth_server.token_url'), [
            'grant_type' => 'authorization_code',
            'client_id' => config('services.auth_server.client_id'),
            'client_secret' => config('services.auth_server.client_secret'),
            'redirect_uri' => config('services.auth_server.redirect'),
            'code' => $request->code,
        ]);

        $tokenData = $response->json();

        if (isset($tokenData['error'])) {
            return redirect('/')->with('error', 'Authentication failed: ' . ($tokenData['message'] ?? 'Unknown error.'));
        }

        // Utilisez le jeton d'accès pour récupérer les informations de l'utilisateur
        $userResponse = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $tokenData['access_token'],
        ])->get(config('services.auth_server.userinfo_url'));

        $userData = $userResponse->json();

        if (isset($userData['error'])) {
            return redirect('/')->with('error', 'Failed to retrieve user info: ' . ($userData['message'] ?? 'Unknown error.'));
        }

        // Créez ou mettez à jour l'utilisateur dans votre base de données locale
        // Utilisez un identifiant unique de l'IdP (par exemple, 'sub' de l'ID Token ou un 'id' de l'userinfo endpoint)
        /**
         * @var Model $user
         */
        $user = config('sso.user_model')::updateOrCreate(
            ['auth_server_id' => $userData['id']], // Assurez-vous d'avoir cette colonne dans votre table users
            [
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => '', // Le mot de passe n'est pas stocké ici
            ]
        );

        // Connectez l'utilisateur localement
        Auth::login($user);

        // Stockez les jetons si vous en avez besoin pour des appels API ultérieurs
        session([
            'access_token' => $tokenData['access_token'],
            'refresh_token' => $tokenData['refresh_token'] ?? null,
            'expires_in' => $tokenData['expires_in'],
        ]);

        return redirect('/dashboard'); // Redirigez vers le tableau de bord de l'application
    }

    public function logout(Request $request)
    {
        // Invalider la session locale de l'application
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Rediriger vers le point de terminaison de déconnexion du serveur d'authentification
        // Le serveur d'authentification gérera la déconnexion de tous les clients
        return redirect(config('sso.server_logout_url'));
    }

    /**
     * Point de terminaison pour la déconnexion silencieuse (utilisé par l'IdP pour le SLO).
     * Il invalide la session locale de l'application sans redirection.
     */
    public function silentLogout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response('Logged out from client.', 200);
    }
}
