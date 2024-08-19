<?php

namespace App\Livewire;

use App\Models\Katedra;
use App\Services\KatedraService;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

#[Title('Pregled katedri')]
class KatedraIndex extends Component
{
    use Toast;
    use WithPagination;

    public $headers;

    public $searchTerm = '';

    public function updatedSearchTerm() {
        $this->resetPage();
    }

    private function loadKatedra() {
        return Katedra::with(['sef:id,ime,prezime,srednje_slovo', 'zamenik:id,ime,prezime,srednje_slovo'])
            ->withCount('aktivniZaposleni')
            ->where('aktivna', 1)
            ->when($this->searchTerm, function ($query) {
                $query->where('naziv_katedre', 'regexp', $this->searchTerm);
            })
            ->paginate(10);
    }

    public function mount() {
        $this->headers = [
            ['key' => 'naziv_katedre', 'label' =>'Naziv', 'class' => 'font-bold text-lg'],
            ['key' => 'sef', 'label' =>'Šef'],
            ['key' => 'zamenik', 'label' =>'Zamenik'],
            ['key' => 'broj_zap', 'label' =>'Broj zaposlenih', 'class' => 'hidden lg:table-cell']
        ];
    }

    public function create() {
        return $this->redirect('/katedra/create', navigate: true);
    }

    public function deleteKatedra($katedra_id) {
        $katedra = Katedra::findOrFail($katedra_id);
        $katedra->update(['aktivna' => 0]);
        $this->success('Katedra '.$katedra->naziv_katedre.' uspešno obirsana.');
    }

    public function render()
    {
        return view('livewire.katedra-index', [
            'katedre' => $this->loadKatedra()
        ]);
    }
}
