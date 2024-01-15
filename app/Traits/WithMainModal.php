<?php

namespace App\Traits;

trait WithMainModal
{
    // open offcanvas modal
    public function openMainModal()
    {
        $this->dispatch('select-container');
        $this->dispatch('open-main-modal');

    }

    // close offcanvas modal
    public function closeMainModal()
    {
        $this->dispatch('close-main-modal');
        $this->form->reset();
        $this->resetValidation();
    }


}
