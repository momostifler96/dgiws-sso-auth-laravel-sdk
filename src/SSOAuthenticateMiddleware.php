<?php

namespace Momoledev\DgiwsAuthLaravelSdk;

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
        if ($request->wantsJson()) {
            if ($request->bearerToken()) {
                $userRequest = Http::withToken($request->bearerToken())->get(env('AUTH_CONTAINER_NAME') . '/api/account/me');
                if ($userRequest->successful()) {
                    $request->merge(['user' => $userRequest->json()['user']]);
                    return $next($request);
                }
            }
            return response()->json(['message' => 'Unauthorized'], 401);
        } else if (!$request->session()->has('access_token') || $request->session()->get('access_token_expires') < Carbon::now()->timestamp || !$request->session()->has('user')) {
            return redirect()->route('sso.login');
        } else {
            $userRequest = Http::withToken($request->session()->get('access_token'))->get(env('AUTH_CONTAINER_NAME') . '/api/account/me');
            if ($userRequest->successful()) {
                $request->merge(['user' => $userRequest->json()['user']]);
                return $next($request);
            }
            Log::error('Une erreur est survenue lors de la récupération des informations de l\'utilisateur.', ['error' => $userRequest->json()]);
            abort(500, 'Une erreur est survenue');
        }
        return $next($request);
    }
}
