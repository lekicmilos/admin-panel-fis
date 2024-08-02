<?php

namespace App\Livewire;

use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Pregled katedri')]
class KatedraIndex extends Component
{
    public function render()
    {
        return view('livewire.katedra-index');
    }
}
