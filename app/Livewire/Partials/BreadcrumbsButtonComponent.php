<?php

namespace App\Livewire\Partials;

use Livewire\Component;

class BreadcrumbsButtonComponent extends Component
{
    public function render()
    {
        return view('livewire.partials.breadcrumbs-button-component');
    }

    public function openCreateKnowledgeBaseModal()
    {
        $this->dispatch('openCreateKnowledgeBaseModal');
    }

    public function openKnowledgeBaseCategoryModal()
    {
        $this->dispatch('openKnowledgeBaseCategoryModal');
    }

    public function openCreateBusinessModal()
    {
        $this->dispatch('openCreateBusinessModal');
    }
}
