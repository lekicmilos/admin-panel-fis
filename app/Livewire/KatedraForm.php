<?php

namespace App\Livewire;

use App\Models\Katedra;
use App\Models\Zaposleni;
use App\Services\KatedraService;
use Carbon\Carbon;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

class KatedraForm extends Component
{
    public $title = '';
    public $katedra = null;

    #[Validate('required|min:3')]
    public $naziv = '';

    //public $selectedZaposleni = null;

    #[Validate([
        'zaposleni' => 'required|min:1',
        'zaposleni.*.datum_od' => 'required|date',
        'zaposleni.*.datum_do' => 'nullable|date|after:zaposleni.*.datum_od'
    ])]
    public $zaposleni = [];

    #[Validate([
        'sef' => 'required',
        'sef.datum_od' => 'required|date',
        'sef.datum_do' => 'nullable|date|after:sef.datum_od'
    ])]
    public $sef = [];

    #[Validate([
        'zamenik' => 'required',
        'zamenik.datum_od' => 'required|date',
        'zamenik.datum_do' => 'nullable|date|after:zamenik.datum_od'
    ])]
    public $zamenik = [];
    public $katedraService;
    public $all_zaposleni = [];

    public $headers;
    public function mount($katedra_id = null)
    {
        $this->all_zaposleni = Zaposleni::all()->map(function ($zap) {
            return [
                'id' => $zap->id,
                'name' => $zap->punoIme(),
            ];
        })->toArray();

        $this->headers = [
            ['key' => 'id', 'label' => '#'],
            ['key' => 'ime', 'label' => 'ime'],
            ['key' => 'datum_od', 'label' => 'Datum od'],
            ['key' => 'datum_do', 'label' => 'Datum do'],
        ];

        $katedraService = new KatedraService();
        if ($katedra_id) {
           $this->title = 'Izmeni katedru';
           $katedra = $katedraService->toDTO(Katedra::findOrFail($katedra_id));
           $naziv = $katedra->naziv;
           $zaposleni = $katedra->zaposleni;
           $sef = $katedra->sef;
           $zamenik = $katedra->zamenik;
        } else {
            $this->title = 'Nova katedra';
            //$katedra =

        }

    }

    public function validateZaposleniTable()
    {
        // Validate specific fields on blur
//        $this->validateOnly('zaposleni');
        $this->validate();
    }

    public function addZaposleni($selectedZaposleni) {
        $zap = Zaposleni::find($selectedZaposleni);
        if ($zap) {
            $this->zaposleni[] = [
                'id' => $selectedZaposleni,
                'ime' => $zap->punoIme(),
                'datum_od' => Carbon::now()->format('Y-m-d'),
                'datum_do' => null,
            ];
        }
    }

    public function removeZaposleni($index)
    {
        // Remove the item from the array
        array_splice($this->zaposleni, $index, 1);
    }

    public function save()
    {
//        $this->validate([
//            'katedra.naziv' => 'required|string|max:255',
////            'sef' => 'required|exists:zaposleni,id',
////            'zamenik' => 'nullable|exists:zaposleni,id',
//            'zaposleniIds.*.datum_od' => 'required|date',
//            'zaposleniIds.*.datum_do' => 'nullable|date|after:zaposleniIds.*.datum_od',
//        ]);

//        $katedraService->upsert()

        return redirect()->route('katedra.index');
    }

    public function render()
    {
        return view('livewire.katedra-form');
    }
}
