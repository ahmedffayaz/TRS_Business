<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Enums\User\AccountType;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class VerifyUserAccountType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        if ($user->hasRole('super-admin') && !$request->routeIs('cms.dashboard') && !$request->routeIs('cms.system-settings') && !$request->routeIs('cms.businesses') ) {
            return redirect()->route('cms.dashboard');
        }

        if ($user->account_type->value === AccountType::CLIENT->value && !$request->session()->has('business') && !request()->routeIs('cms.dashboard') && !$request->routeIs('cms.system-settings') && !$request->routeIs('cms.businesses')) {
            if ($user?->client) {
                $userBusiness = $user->client->business;
                session(['business' => $userBusiness->name]);
            } else if ($user->hasRole(['super-admin', 'admin'])) {
                return redirect()->route('dashboard.home');
            } else {
                session(['business' => $user->business->name]);
                return redirect()->route('dashboard.home');
            }
        }

        return $next($request);
    }
}
