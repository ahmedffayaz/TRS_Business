<?php

namespace App\Livewire\Partials;

use Livewire\Component;

class BreadcrumbsComponent extends Component
{
    public $breadcrumbs = [];
    public $page_title;

    public function mount()
    {
        $this->generateBreadcrumbs();
    }

    public function render()
    {
        return view('livewire.partials.breadcrumbs-component');
    }

    private function generateBreadcrumbs()
    {
        $url = url('/');
        $currentUrlWithoutBase = str_replace($url, '', url()->current());
        $segments = explode('/', trim($currentUrlWithoutBase, '/'));
        $breadcrumbs = [];
        if(isset($segment[0]) && $segment[0] == 'dashboard'){
            $breadcrumbs = [
                ['url' => url('/'), 'label' => 'Dashboard'],
            ];
        }
        foreach($segments as $segment){
           $page = ['url' => asset($segment), 'label' => ucwords($segment)];
           array_push($breadcrumbs, $page);
           $this->page_title = ucwords($segment);
        }
        $this->breadcrumbs = $breadcrumbs;
    }
}
