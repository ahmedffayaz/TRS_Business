<?php

namespace App\Providers;

use App\Models\KnowledgeBaseQa;
use Illuminate\Support\Facades\View;
use Illuminate\Database\Schema\Builder;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        Builder::defaultStringLength(191);
    }
}
