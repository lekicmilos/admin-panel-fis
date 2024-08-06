<?php

namespace App\Livewire;

use App\Models\Katedra;
use App\Models\Zvanje;
use App\Services\KatedraService;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Pregled zvanja')]
class ZvanjeIndex extends Component
{

    public $zvanja = [];
    public $headers;
    public bool $deleteModal = false;

    public function mount() {
        $this->zvanja = Zvanje::all()->sortBy('nivo', descending: true);
        $this->headers = [
            ['key' => 'naziv_zvanja', 'label' =>'Naziv'],
            ['key' => 'nivo', 'label' =>'Nivo'],
        ];
    }

    public function create() {
        return $this->redirect('/zvanje/create', navigate: true);
    }

//    public function edit($zvanje_id) {
//        $this->redirectRoute('zvanje.edit',  ['zvanje_id' => $zvanje_id]);
//    }

    public function deleteZvanje($zvanje_id) {
        $zvanje = Zvanje::findOrFail($zvanje_id);
        $zvanje->delete();
        $this->zvanja = Zvanje::all();
        $this->deleteModal = false;
    }

}
