<?php

namespace App\Livewire;

use App\Models\Zaposleni;
use Livewire\Component;

class ZaposleniSelect extends Component
{
    public $zaposleni = [];
    public $selectedId = null;


    /*public function updatedSelectedId($zapId)
    {
        $selectedZap = array_filter($this->zaposleni, function ($zap) use ($zapId) {
            return $zap['id'] === $zapId;
        });
        $this->dispatch('zaposleni-selected', zaposleni: $zapId);
    }*/
    public function render()
    {
        return view('livewire.zaposleni-select');
    }
}
