<div>
    <h1 class="text-3xl font-bold py-4">{{ $title }}</h1>

    <x-hr />

    <x-form wire:submit="save" class="space-y-4" no-separator>

        <!-- Naziv Input -->
        <div class="w-96">
            <x-input
                    id="naziv"
                    type="text"
                    label="Naziv"
                    wire:model.blur="naziv"
                    required
            />
        </div>

        <!-- Sef Selection -->
        <div class="flex items-center space-x-4 flex-grow">
            <x-select label="Šef" :options="$all_zaposleni" wire:model="sef.id" placeholder="--Izaberi šefa--" required></x-select>
            <x-input label="Datum od" type="date" wire:model="sef.datum_od" required/>
            <x-input label="Datum do" type="date" wire:model="sef.datum_do" />
{{--            <x-datepicker label="Alt" wire:model="sef.datum_do" icon="o-calendar" :config="$config1" />--}}
        </div>

        <!-- Zamenik Selection -->
        <div class="flex items-center space-x-4 flex-grow">
            <x-select label="Zamenik" :options="$all_zaposleni" wire:model="zamenik.id" placeholder="--Izaberi zamenika--" required></x-select>
            <x-input label="Datum od" type="date" wire:model="zamenik.datum_od" required />
            <x-input label="Datum do" type="date" wire:model="zamenik.datum_do"/>
        </div>

        <!-- Dodaj zaposleni button -->
        <div x-data="{ selectedZaposleni: '' }" class="w-fit flex items-end space-x-4">
            <x-select label="Zaposleni" :options="$all_zaposleni" x-model="selectedZaposleni" placeholder="--Izaberi zaposlenog--"></x-select>
            <x-button icon='o-plus' label="Dodaj zaposlenog" @click="$wire.addZaposleni(selectedZaposleni)" class="btn-outline" spinner="addZaposleni"/>
        </div>

        <!-- Zaposleni Table -->
        @if(!empty($zaposleni))
        <div class="w-fit shadow-lg dark:shadow-neutral">
            <x-table :headers="$headers" :rows="$zaposleni" wire:model="zaposleni" >
                @scope('cell_datum_od', $zap)
                <x-input type="date" :value="$zap['datum_od']" wire:model="zaposleni.{{ $loop->index }}.datum_od" />
                @endscope

                @scope('cell_datum_do', $zap)
                <x-input type="date" :value="$zap['datum_do']" wire:model="zaposleni.{{ $loop->index }}.datum_do" />
                @endscope

                @scope('actions', $zap)
                <x-button icon="o-trash" wire:click="removeZaposleni({{ $loop->index }})" spinner class="btn-sm" />
                @endscope
            </x-table>
        </div>
        @endif

        <x-slot:actions>
            <x-button label="Sačuvaj" class="btn-primary" type="submit" spinner="save" />
        </x-slot:actions>

    </x-form>
</div>
