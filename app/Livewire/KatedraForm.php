<?php

namespace App\Livewire;

use App\DTO\KatedraDTO;
use App\DTO\ZaposleniNaKatedriDTO;
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

    public $katedra_id = null;

    #[Validate('required|min:3')]
    public $naziv = '';

    #[Validate([
        'zaposleni' => 'required|min:1',
        'zaposleni.*.datum_od' => 'required|date',
        'zaposleni.*.datum_do' => 'nullable|date|after:zaposleni.*.datum_od'
    ])]
    public $zaposleni = [];

    #[Validate([
        'sef' => 'required',
        'sef.id' => 'required|exists:zaposleni,id',
        'sef.datum_od' => 'required|date',
        'sef.datum_do' => 'nullable|date|after:sef.datum_od'
    ])]
    public $sef = ['id' => null, 'datum_od' => null, 'datum_do' => null];

    #[Validate([
        'zamenik' => 'required',
        'zamenik.id' => 'required|exists:zaposleni,id',
        'zamenik.datum_od' => 'required|date',
        'zamenik.datum_do' => 'nullable|date|after:zamenik.datum_od'
    ])]
    public $zamenik = ['id' => null, 'datum_od' => null, 'datum_do' => null];
    //public $katedraService;
    public $all_zaposleni = [];

    public $headers;
    public function mount($katedra_id = null)
    {
        // load the combo box data
        $this->all_zaposleni = Zaposleni::all()->map(function ($zap) {
            return [
                'id' => $zap->id,
                'name' => $zap->punoIme(),
            ];
        })->toArray();

        // mini table headers
        $this->headers = [
            ['key' => 'id', 'label' => '#', 'hidden' => 'true'],
            ['key' => 'ime', 'label' => 'IME', 'class' => 'w-48'],
            ['key' => 'datum_od', 'label' => 'DATUM OD', 'class' => 'w-32'],
            ['key' => 'datum_do', 'label' => 'DATUM DO', 'class' => 'w-32'],
        ];

        // if in edit mode load the fields
        if ($katedra_id) {
           $this->title = 'Izmeni katedru';
           $katedra = Katedra::with(['angazovanje', 'pozicija'])->findOrFail($katedra_id);
           $katedraDTO = (new \App\Services\KatedraService)->toDTO($katedra);
           $this->naziv = $katedraDTO->naziv;
           $this->zaposleni = array_map(function ($zap) {return (array) $zap;}, $katedraDTO->zaposleni);
           $this->sef = (array) $katedraDTO->sef;
           $this->zamenik = (array) $katedraDTO->zamenik;
        } else {
            $this->title = 'Nova katedra';
        }

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

    private function prepareDate($date) {
        return $date=='' ? null : $date;
    }

    public function save()
    {
        $this->validate();

        $sef = new ZaposleniNaKatedriDTO($this->sef['id'],null,
            $this->prepareDate($this->sef['datum_od']),
            $this->prepareDate($this->sef['datum_do']));

        $zamenik = new ZaposleniNaKatedriDTO($this->zamenik['id'],null,
            $this->prepareDate($this->zamenik['datum_od']),
            $this->prepareDate($this->zamenik['datum_do']));

        $zaposleni = [];
        foreach ($this->zaposleni as $zap)
            $zaposleni[] = new ZaposleniNaKatedriDTO($zap['id'], null,
                $this->prepareDate($zap['datum_od']),
                $this->prepareDate($zap['datum_do']));

        $katedraDTO = new KatedraDTO($this->katedra_id,$this->naziv,true,$zaposleni,$sef,$zamenik);

        (new \App\Services\KatedraService)->upsert($katedraDTO);

        return redirect()->route('katedra.index');
    }

    public function render()
    {
        return view('livewire.katedra-form');
    }
}
