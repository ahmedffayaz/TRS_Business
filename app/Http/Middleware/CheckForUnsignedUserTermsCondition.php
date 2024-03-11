<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\TermsConditionUser;
use App\Traits\UserTermsCondition;
use Symfony\Component\HttpFoundation\Response;

class CheckForUnsignedUserTermsCondition
{
    use UserTermsCondition;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = request()->user();

        if (!$this->user->hasRole('super-admin'))
            session(['business' => $this?->user?->client?->business?->name]);

        $hasTermsConditions = $this->getUserTermsRoles()->exists();
        if ($hasTermsConditions) {
            $termConditionUser = TermsConditionUser::where('user_id', $user->id)->exists();

            if ($termConditionUser && request()->routeIs('dashboard.terms-conditions.accept'))
                return redirect()->route('dashboard.home');
            if(!$termConditionUser && !request()->routeIs('dashboard.terms-conditions.accept')){
                return redirect()->route('dashboard.terms-conditions.accept');}
        }

        return $next($request);
    }
}
