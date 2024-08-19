<?php

namespace App\Livewire;

use App\DTO\ZvanjeDTO;
use App\Models\Zvanje;
use App\Services\ZvanjeService;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ZvanjeForm extends Component
{
    public $title = '';

    public $zvanje_id = null;

    public $naziv = '';

    #[Validate('required|numeric')]
    public $nivo = '';


    public function mount($zvanje_id = null)
    {
        // if in edit mode load the fields
        if ($zvanje_id) {
           $this->title = 'Izmeni zvanje';
           $zvanje = Zvanje::findOrFail($zvanje_id);
           $this->naziv = $zvanje->naziv_zvanja;
           $this->nivo = $zvanje->nivo;
        } else {
            $this->title = 'Novo zvanje';
        }

    }

    public function rules() {
        return [
            'naziv' => "required|min:3|unique:zvanje,naziv_zvanja,$this->zvanje_id"
        ];
    }

    public function save()
    {
        $this->validate();

        $zvanjeDTO = new ZvanjeDTO($this->zvanje_id, $this->naziv, $this->nivo);
        (new ZvanjeService())->upsert($zvanjeDTO);

        return redirect()->route('zvanje.index');
    }

}
