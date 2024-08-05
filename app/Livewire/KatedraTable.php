<?php

namespace App\Livewire;

use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Katedra;
use Rappasoft\LaravelLivewireTables\Views\Columns\ButtonGroupColumn;
use Rappasoft\LaravelLivewireTables\Views\Columns\LinkColumn;

class KatedraTable extends DataTableComponent
{
    protected $model = Katedra::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function builder(): Builder
    {
        return Katedra::with('pozicija')->where('aktivna', 1);
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable(),
            Column::make("Naziv", "naziv_katedre")
                ->sortable()
                ->searchable(),
            Column::make("Šef")
                ->label(fn($row) => $row->sef() ?? 'Nema')
                ->sortable(),
            Column::make("Zamenik")
                ->label(fn($row) => $row->zamenik() ?? 'Nema')
                ->sortable(),
            ButtonGroupColumn::make('Actions')
                ->attributes(function($row) {
                    return [
                        'class' => 'space-x-2',
                    ];
                })
                ->buttons([
                    LinkColumn::make('Izmeni')
                        ->title(fn($row) => 'Izmeni')
                        ->location(fn($row) => route('katedra.edit', ['katedra_id' => $row->id]))
                        ->attributes(function($row) {
                            return [
                                'target' => '_blank',
                                'class' => 'underline text-blue-500 hover:no-underline',
                            ];
                        }),
                    LinkColumn::make('Obriši')
                        ->title(fn($row) => 'Obriši')
                        ->location(fn($row) => "route('katedra.destroy', ['katedra_id' => $row->id])")
                        ->attributes(function($row) {
                            return [
                                'class' => 'underline text-blue-500 hover:no-underline',
                                'wire:confirm' => "Are you sure you want to delete this post?"
                            ];
                        }),
                ]),
            ];
    }
}
