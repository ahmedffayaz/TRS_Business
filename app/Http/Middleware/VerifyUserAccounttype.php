<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Enums\User\AccountType;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class VerifyUserAccounttype
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        if ($user->hasRole(['super-admin', 'admin']) && !request()->routeIs('select-business') && !$request->session()->has('business')) {
            return redirect()->route('select-business');
        } else if ($user->account_type === AccountType::CLIENT->value) {
            $userBusiness = $user->client->business;
            session(['business' => $userBusiness->name]);
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
