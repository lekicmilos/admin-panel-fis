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

    #[Validate('required|min:3')]
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

    public function save()
    {
        $this->validate();

        $zvanjeDTO = new ZvanjeDTO($this->zvanje_id, $this->naziv, $this->nivo);
        (new ZvanjeService())->upsert($zvanjeDTO);

        return redirect()->route('zvanje.index');
    }

}
