<?php

namespace App\Livewire;

use App\DTO\KatedraDTO;
use App\DTO\ZaposleniNaKatedriDTO;
use App\Models\Katedra;
use App\Models\Zaposleni;
use App\Services\KatedraService;
use Carbon\Carbon;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

class KatedraForm extends Component
{
    use Toast;

    public $title = '';

    #[Locked]
    public $katedra_id = null;

    #[Validate('required|min:3')]
    public $naziv = '';

    #[Validate([
        'zaposleni' => 'required|min:1',
        'zaposleni.*.datum_od' => 'required|date',
        'zaposleni.*.datum_do' => 'nullable|date|after:zaposleni.*.datum_od'
    ])]
    public $zaposleni = [];
    public $zaposleni_rows = [];

    #[Validate([
        'sef.id' => 'required|exists:zaposleni,id',
        'sef.datum_od' => 'required|date',
        'sef.datum_do' => 'nullable|date|after:sef.datum_od'
    ])]
    public $sef = ['id' => null, 'datum_od' => null, 'datum_do' => null];

    #[Validate([
        'zamenik.id' => 'required|exists:zaposleni,id|different:sef.id',
        'zamenik.datum_od' => 'required|date',
        'zamenik.datum_do' => 'nullable|date|after:zamenik.datum_od'
    ])]
    public $zamenik = ['id' => null, 'datum_od' => null, 'datum_do' => null];

    public array $headers;

    private function mapDTOtoArray(?ZaposleniNaKatedriDTO $zap)
    {
        $danas = Carbon::now();

        return [
            'id' => (string)$zap?->id,
            'ime' => $zap?->ime,
            'datum_od' => $zap?->datum_od,
            'datum_do' => $zap?->datum_do,
            'aktivan' => $zap && $zap->datum_od <= $danas && (is_null($zap->datum_do) || $zap->datum_do >= $danas),
        ];
    }

    public function mount($katedra_id = null)
    {
        // mini table headers
        $this->headers = [
            ['key' => 'id', 'label' => '#', 'hidden' => 'true'],
            ['key' => 'ime', 'label' => 'ime', 'class' => 'min-w-48 text-lg'],
            ['key' => 'datum_od', 'label' => 'datum od', 'class' => 'w-32'],
            ['key' => 'datum_do', 'label' => 'datum do', 'class' => 'w-32'],
        ];

        // if in edit mode load the fields
        if ($katedra_id) {
           $this->title = 'Izmeni katedru';
           $katedra = Katedra::findOrFail($katedra_id);
           $katedraDTO = (new KatedraService)->toDTO($katedra);
           $this->naziv = $katedraDTO->naziv;

           $this->zaposleni = array_map([$this, 'mapDTOtoArray'], $katedraDTO->zaposleni);
           usort($this->zaposleni, fn($a, $b) => $b['aktivan'] <=> $a['aktivan']);
           $this->applyFilter(false);

           $this->sef = $this->mapDTOtoArray($katedraDTO->sef);
           $this->zamenik = $this->mapDTOtoArray($katedraDTO->zamenik);
        } else {
            $this->title = 'Nova katedra';
        }
    }

    public function applyFilter($show_all) {
        $this->zaposleni_rows = array_filter($this->zaposleni,
            !$show_all ? fn($zap) => $zap['aktivan'] : null);
    }

    public function addZaposleni($selectedZaposleni) {
        if (!empty($selectedZaposleni) && $zap = Zaposleni::find($selectedZaposleni)) {
            $new_zap = [
                'id' => $selectedZaposleni,
                'ime' => $zap->punoIme(),
                'datum_od' => Carbon::now()->format('Y-m-d'),
                'datum_do' => null,
                'aktivan' => true,
            ];

            array_unshift($this->zaposleni, $new_zap);
            array_unshift($this->zaposleni_rows, $new_zap);
        } else {
            $this->addError('zaposleni-select', 'Nije izabran ni jedan zaposleni');
        }
    }

    public function removeZaposleni($index) {
        array_splice($this->zaposleni, $index, 1);
        array_splice($this->zaposleni_rows, $index, 1);
    }

    private function prepareDate($date) {
        return empty($date) ? null : Carbon::parse($date)->format('Y-m-d');
    }

    public function save()
    {
        // show inactive emp if there are errors
        $this->applyFilter(true);

        $this->validate();

        // convert properties to DTO
        $sef = new ZaposleniNaKatedriDTO($this->sef['id'],null,
            $this->prepareDate($this->sef['datum_od']),
            $this->prepareDate($this->sef['datum_do']));

        $zamenik = new ZaposleniNaKatedriDTO($this->zamenik['id'],null,
            $this->prepareDate($this->zamenik['datum_od']),
            $this->prepareDate($this->zamenik['datum_do']));

        $zaposleni = [];
        foreach ($this->zaposleni as $zap) {
            $zaposleni[] = new ZaposleniNaKatedriDTO($zap['id'], null,
                $this->prepareDate($zap['datum_od']),
                $this->prepareDate($zap['datum_do']));
        }

        // add additional validation to the existing validator
        $this->withValidator(function ($validator) use ($zaposleni) {
            $validator->after(function ($validator) use ($zaposleni) {
                if($error_index = KatedraService::validateDuplicate($zaposleni))
                    $validator->errors()->add('zaposleni.'.$error_index.'.datum_od', 'Zaposleni je već dodat u tom periodu');
            });
        });

        $this->validate();

        $katedraDTO = new KatedraDTO($this->katedra_id,$this->naziv,true,$zaposleni,$sef,$zamenik);
        (new KatedraService)->upsert($katedraDTO);

        $this->success(
            ''.$this->naziv.' uspešno '.($this->katedra_id ? 'sačuvana' : 'ažurirana').'!',
            redirectTo: '/katedra'
        );
    }

    public function render()
    {
        // load the combo box data
        $all_zaposleni = Zaposleni::all(['id', 'ime', 'prezime', 'srednje_slovo'])
            ->sortBy(['ime', 'sredjne_slovo'])
            ->map(function ($zap) {
                return [
                    'id' => $zap->id,
                    'name' => $zap->punoIme(),
                ];
            })
            ->toArray();

        $inactive_zaposleni_decoration = [
            'inactive-zaposleni' => fn($zap) => !$zap['aktivan'],
        ];

        return view('livewire.katedra-form')->with([
            'row_decoration' => $inactive_zaposleni_decoration,
            'all_zaposleni' => $all_zaposleni
        ]);
    }
}
