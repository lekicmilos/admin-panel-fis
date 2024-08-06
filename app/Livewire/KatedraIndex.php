<?php

namespace App\Livewire;

use App\Models\Katedra;
use App\Services\KatedraService;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Pregled katedri')]
class KatedraIndex extends Component
{

    public $katedre = [];
    public $headers;
    public bool $deleteModal = false;

    public function mount() {
        $this->katedre = Katedra::with(['pozicija', 'angazovanje'])->where('aktivna', 1)->get();
        $this->headers = [
            ['key' => 'naziv_katedre', 'label' =>'Naziv'],
            ['key' => 'sef', 'label' =>'Šef'],
            ['key' => 'zamenik', 'label' =>'Zamenik'],
            ['key' => 'broj_zap', 'label' =>'Broj zaposlenih', 'class' => 'hidden lg:table-cell']
        ];
    }

    public function create() {
        return $this->redirect('/katedra/create', navigate: true);
    }

    public function edit($katedra_id) {
        $this->redirectRoute('katedra.edit',  ['katedra_id' => $katedra_id]);
    }

    public function deleteKatedra($katedra_id) {
        $katedra = Katedra::findOrFail($katedra_id);
        $katedra->update(['aktivna' => 0]);
        $this->katedre = Katedra::with('pozicija')->where('aktivna', 1)->get();
        $this->deleteModal = false;
    }

    public function render()
    {
        return view('livewire.katedra-index');
    }
}
