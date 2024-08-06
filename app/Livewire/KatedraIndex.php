<?php

namespace App\Livewire;

use App\Models\Katedra;
use App\Services\KatedraService;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Pregled katedri')]
class KatedraIndex extends Component
{
    use WithPagination;

//    public $katedre = [];
    public $headers;

    private function loadKatedra() {
        return Katedra::with(['pozicija', 'angazovanje'])
            ->where('aktivna', 1)
            ->paginate(10);
    }

    public function mount() {
        $this->headers = [
//            ['key' => 'id', 'label' => '#'],
            ['key' => 'naziv_katedre', 'label' =>'Naziv', 'class' => 'font-bold'],
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
    }

    public function render()
    {
        return view('livewire.katedra-index', [
            'katedre' => $this->loadKatedra()
        ]);
    }
}
