<?php

namespace App\Livewire\Backend\KnowledgeBase;

use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Knowledge Base')]
class KnowledgeBaseComponent extends Component
{
    public function render()
    {
        return view('livewire.backend.knowledge-base.knowledge-base-component');
    }
}
