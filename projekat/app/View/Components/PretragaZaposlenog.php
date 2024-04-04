<?php

namespace App\View\Components;

use App\Models\Zaposleni;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PretragaZaposlenog extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public $id,
        public $name,
        public $zaposleni
    )
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.pretraga-zaposlenog');
    }
}
