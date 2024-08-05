<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SetSessionData
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->session()->has('user')) {
            $user = Auth::user();
            $session_data = ['id' => $user->id,
                            'first_name' => $user->first_name,
                            'last_name' => $user->last_name,
                            'email' => $user->email,
                            'business_id' => $user->business_id,
                            'currency' => $user->currency,
                            ];
            $request->session()->put('user', $session_data);
            if(!$user->hasRole('super-admin')) {
                $business = Business::findOrFail($user->business_id);
                $request->session()->put('business_details', $business);
            }
            
        }
        return $next($request);
    }
}
