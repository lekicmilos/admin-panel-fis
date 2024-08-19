<?php

namespace App\Livewire;

use App\Models\Katedra;
use App\Models\Zaposleni;
use App\Models\Zvanje;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

#[Title('Pregled zaposlenih')]
class ZaposleniIndex extends Component
{
    use Toast;
    use WithPagination;

    public $drawer = false;

    public array $search = [
        'ime' => '',
        'fis' => '',
        'zvanje' => null,
        'katedra' => null,
    ];

    public array $sortBy = ['column' => 'ime', 'direction' => 'asc'];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function headers()
    {
        return [
            ['key' => 'fis_broj', 'label' => 'FIS #'],
            ['key' => 'ime', 'label' => 'Ime i prezime', 'class' => 'font-bold text-lg'],
            ['key' => 'email', 'label' => 'Email'],
            ['key' => 'katedra', 'label' => 'Katedra', 'sortable' => false],
            ['key' => 'zvanje', 'label' => 'Zvanje', 'sortable' => false]
        ];
    }

    public function clearFilters()
    {
        $this->search = [
            'ime' => '',
            'fis' => '',
            'zvanje' => null,
            'katedra' => null,
        ];
    }

    private function loadZaposleni()
    {
        return Zaposleni::with(['katedra:id,naziv_katedre', 'zvanje:id,naziv_zvanja'])
            ->where('active', 1)
            ->when(strlen($this->search['ime']) > 3, function ($query) {
                $query->whereFullText(['ime', 'prezime', 'srednje_slovo', 'email'], $this->search['ime'], ['mode' => 'boolean']);
            })
            ->when($this->search['fis'], function ($query) {
                $query->where('fis_broj', 'like', $this->search['fis'] . '%');
            })
            ->when($this->search['katedra'], function ($query) {
                $query->whereHas('katedra', function ($query) {
                    $query->where('katedra_id', $this->search['katedra']);
                });
            })
            ->when($this->search['zvanje'], function ($query) {
                $query->whereHas('zvanje', function ($query) {
                    $query->where('zvanje_id', $this->search['zvanje']);
                });
            })
            ->orderBy(...array_values($this->sortBy))
            ->paginate(10);
    }

    public function create()
    {
        return $this->redirect('/zaposleni/create', true);
    }

    public function deleteZaposleni($zaposleni_id)
    {
        //
    }

    public function render()
    {
        $katedra_options = Katedra::all(['id', 'naziv_katedre', 'aktivna'])
            ->where('aktivna', 1)
            ->sortBy('naziv_katedre')
            ->map(function ($katedra) {
                return [
                    'id' => $katedra->id,
                    'name' => $katedra->naziv_katedre,
                ];
            })
            ->toArray();

        $zvanje_options = Zvanje::all(['id', 'naziv_zvanja'])
            ->map(function ($zvanje) {
                return [
                    'id' => $zvanje->id,
                    'name' => $zvanje->naziv_zvanja,
                ];
            })
            ->toArray();

        return view('livewire.zaposleni-index', [
            'katedra_options' => $katedra_options,
            'zvanje_options' => $zvanje_options,
            'zaposleni' => $this->loadZaposleni(),
            'headers' => $this->headers(),
        ]);
    }
}
