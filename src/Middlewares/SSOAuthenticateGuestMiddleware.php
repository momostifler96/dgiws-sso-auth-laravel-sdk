<?php

namespace Momoledev\DgiwsAuthLaravelSdk\Middlewares;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SSOAuthenticateGuestMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->wantsJson()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if ($request->session()->has('access_token') && $request->session()->has('user')) {
            return redirect('/');
        }
        return $next($request);
    }
}
