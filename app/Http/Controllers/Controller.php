<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, Authorizable;

    protected $auth_user;
    protected $company;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (auth()->check()) {
                $this->auth_user = auth()->user();
                $this->company = $this->auth_user->company;

                view()->composer(['*'], function ($view) {
                    $view->with(['auth_user' => $this->auth_user, 'auth_company' => $this->company]);
                });
            }
            return $next($request);
        });
    }

    public function authorize()
    {
        return true;
    }
}
