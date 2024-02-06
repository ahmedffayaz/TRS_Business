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

        View::composer('*', function ($view) {
            $breadcrumbs = $this->generateBreadcrumbs();

            $view->with('pageBreadcrumbs', $breadcrumbs);
        });
    }

    private function generateBreadcrumbs()
    {
        $pagereadcrumbs = [];

        $currentUrl = Request::path();
        $urlSegments = explode('/', $currentUrl);
        $urlSegmentsIndex = array_search('dashboard', $urlSegments);
        $currentModule = isset($urlSegments[$urlSegmentsIndex + 1]) ? $urlSegments[$urlSegmentsIndex + 1] : '';
        // Add your logic to generate breadcrumbs based on the URL or any other condition
        if (in_array($currentModule, ['knowledge-bases'])) {
            $breadcrumbs = [
                ['title' => 'Dashboard', 'url' => '/'],
                ['title' =>  ucwords(str_replace('-', ' ', $currentModule)), 'url' => ''],
            ];
        } else if (in_array($currentModule, ['knowledge-base-topics'])) {
            $breadcrumbs = [
                ['title' => 'Dashboard', 'url' => '/'],
                ['title' =>  'Knowledge Bases', 'url' => route('dashboard.knowledge-bases.index')],
                ['title' =>  ucwords(str_replace('-', ' ', $currentModule)), 'url' => ''],
            ];
        } else if (in_array($currentModule, ['knowledge-base-question'])) {
            $slug = getSlug($currentUrl);
            $topicSlug = KnowledgeBaseQa::whereSlug($slug)->first()->topic->knowledgeBase->slug;
            $breadcrumbs = [
                ['title' => 'Dashboard', 'url' => '/'],
                ['title' =>  'Knowledge Bases', 'url' => route('dashboard.knowledge-bases.index')],
                ['title' =>  'Knowledge Base Topics', 'url' => route('dashboard.knowledge-base-topics.index', $topicSlug)],
                ['title' =>  ucwords(str_replace('-', ' ', $currentModule)), 'url' => ''],
            ];
        } else {
            $breadcrumbs[] = ['title' => 'Dashboard', 'url' => '/'];
        }

        return $breadcrumbs;
    }
}
