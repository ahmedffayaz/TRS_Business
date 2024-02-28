<?php

namespace App\Livewire\Backend\KnowledgeBase;

use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Search Knowledge Base Keyword')]
class SearchKnowledgeBaseKeywordComponent extends Component
{
    public function render()
    {
        return view('livewire.backend.knowledge-base.search-knowledge-base-keyword-component');
    }
}
