<?php

namespace App\Livewire;

use App\DTO\KatedraZaposlenogDTO;
use App\DTO\ZaposleniDTO;
use App\DTO\ZvanjeZaposlenogDTO;
use App\Models\Katedra;
use App\Models\Zaposleni;
use App\Models\Zvanje;
use App\Services\ZaposleniService;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Mary\Traits\Toast;

class ZaposleniForm extends Component
{
    use Toast;

    public $title = '';
    #[Locked]
    public $zaposleni_id = null;
    #[Validate('required|string|min:3')]
    public $ime;
    #[Validate('required|string|min:3')]
    public $prezime;
    #[Validate('required|string|max:1')]
    public $srednje_slovo;
    #[Validate('required|email')]
    public $email;
    #[Validate('required|in:Muski,Zenski')]
    public $pol;
    public $fis_broj;
    #[Validate([
        'katedra.id' => 'required|exists:katedra,id',
        'katedra.datum_od' => 'required|date',
        'katedra.datum_do' => 'nullable|date|after:katedra.datum_od'
    ])]
    public $katedra = ['id' => null, 'datum_od' => null, 'datum_do' => null];
    #[Validate([
        'zvanje.id' => 'required|exists:zvanje,id',
        'zvanje.datum_od' => 'required|date',
        'zvanje.datum_do' => 'nullable|date|after:zvanje.datum_od'
    ])]
    public array $zvanje = ['id' => null, 'datum_od' => null, 'datum_do' => null];
//    #[Validate('required')]
    public $u_penziji;
    #[Validate('nullable|date|required_if:u_penziji,true')]
    public $datum_penzije;

    public function mount($zaposleni_id = null)
    {
        if ($zaposleni_id) {
            $this->title = 'Izmeni zaposlenog';
            $zaposleni = Zaposleni::findOrFail($zaposleni_id);
            $zaposleniDTO = ZaposleniService::toDTO($zaposleni);

            // Map the zaposleni DTO to component properties
            $this->zaposleni_id = $zaposleniDTO->id;
            $this->ime = $zaposleniDTO->ime;
            $this->prezime = $zaposleniDTO->prezime;
            $this->srednje_slovo = $zaposleniDTO->srednje_slovo;
            $this->email = $zaposleniDTO->email;
            $this->pol = $zaposleniDTO->pol;
            $this->fis_broj = $zaposleniDTO->fis_broj;
            $this->u_penziji = $zaposleniDTO->u_penziji;
            $this->datum_penzije = $zaposleniDTO->datum_penzije;

            // Map katedra DTO
            if ($zaposleniDTO->katedra) {
                $this->katedra = [
                    'id' => $zaposleniDTO->katedra->id,
                    'datum_od' => $zaposleniDTO->katedra->datum_od,
                    'datum_do' => $zaposleniDTO->katedra->datum_do,
                ];
            } else {
                $this->katedra = ['id' => null, 'datum_od' => null, 'datum_do' => null];
            }

            // Map zvanje DTO
            if ($zaposleniDTO->zvanje) {
                $this->zvanje = [
                    'id' => $zaposleniDTO->zvanje->id,
                    'datum_od' => $zaposleniDTO->zvanje->datum_od,
                    'datum_do' => $zaposleniDTO->zvanje->datum_do,
                ];
            } else {
                $this->zvanje = ['id' => null, 'datum_od' => null, 'datum_do' => null];
            }
        } else {
            $this->title = 'Novi zaposleni';
        }
    }

    public function rules() {
        return [
            'fis_broj' => "required|numeric|unique:zaposleni,fis_broj,$this->zaposleni_id"
        ];
    }

    private function prepareDate($date) {
        return empty($date) ? null : Carbon::parse($date)->format('Y-m-d');
    }

    public function save()
    {
        $this->validate();

        $zaposleniDTO = new ZaposleniDTO(
            $this->zaposleni_id,
            $this->ime,
            $this->prezime,
            $this->srednje_slovo,
            $this->email,
            $this->pol,
            $this->fis_broj,
            $this->u_penziji ?? false,
            $this->prepareDate($this->datum_penzije),
            $this->katedra ? new KatedraZaposlenogDTO(
                $this->katedra['id'],
                null,
                $this->prepareDate($this->katedra['datum_od']),
                $this->prepareDate($this->katedra['datum_do']),
            ) : null,
            $this->zvanje ? new ZvanjeZaposlenogDTO(
                $this->zvanje['id'],
                null,
                $this->prepareDate($this->zvanje['datum_od']),
                $this->prepareDate($this->zvanje['datum_do']),
            ) : null,
        );

        ZaposleniService::upsert($zaposleniDTO);

        $this->success(
            "Zaposleni $this->ime $this->prezime uspešno ".($this->zaposleni_id ? 'sačuvan' : 'ažuriran')."!",
            redirectTo: '/zaposleni'
        );
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
        $pol_options = [
            ['id' => 'Muski', 'name' => 'Muški'],
            ['id' => 'Zenski', 'name' => 'Ženski'],
        ];

        return view('livewire.zaposleni-form', [
                'katedra_options' => $katedra_options,
                'zvanje_options' => $zvanje_options,
                'pol_options' => $pol_options,
            ]
        );
    }
}
