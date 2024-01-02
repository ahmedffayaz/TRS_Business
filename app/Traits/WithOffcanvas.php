<?php

namespace App\Traits;

trait WithOffcanvas
{
    // open offcanvas modal
    public function openOffcanvas()
    {
        $this->dispatch('open-offcanvas');
    }

    // close offcanvas modal
    public function closeOffcanvas()
    {
        $this->dispatch('close-offcanvas');
        $this->form->reset();
        $this->resetValidation();
    }
}
