<?php

namespace Momoledev\DgiwsAuthLaravelSdk\Middlewares;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SSOAuthenticateMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user_info_url = config('sso.docker_container_name') . config('sso.user_info_path');
        if ($request->wantsJson()) {
            if ($request->bearerToken()) {
                $userRequest = Http::withToken($request->bearerToken())->get($user_info_url);
                if ($userRequest->successful()) {
                    $request->merge(['user' => $userRequest->json()['user']]);
                    return $next($request);
                }
            }
            return response()->json(['message' => 'Unauthorized'], 401);
        } else if ($request->session()->has('access_token')) {
            $userRequest = Http::withToken($request->session()->get('access_token'))->get($user_info_url);
            if ($userRequest->successful()) {
                $request->merge(['user' => $userRequest->json()['user']]);
                return $next($request);
            } else if ($userRequest->status() == 401) {
                session()->forget(['access_token', 'user']);
                return redirect()->route(config('sso.login_route.name'));
            }
            Log::error('Une erreur est survenue lors de la récupération des informations de l\'utilisateur.', ['error' => $userRequest->json()]);
            abort(500, 'Une erreur est survenue');
        } else {
            return redirect()->route(config('sso.login_route.name'));
        }
        return $next($request);
    }
}
