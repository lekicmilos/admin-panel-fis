<div>
    <x-toast />
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
            <x-select
                label="Zaposleni"
                :options="$all_zaposleni"
                x-model="selectedZaposleni"
                placeholder="--Izaberi zaposlenog--"
                error-field="zaposleni-select"
            />
            <x-button icon='o-plus' label="Dodaj zaposlenog" @click="$wire.addZaposleni(selectedZaposleni)" class="btn-accent text-bg-content" spinner="addZaposleni"/>
        </div>

        <!-- Zaposleni Table -->
        @if(!empty($zaposleni))
            <div class="table relative w-max min-w-[40rem] m-2" x-data="{ showAll: false }">

                <!-- Container for checkbox and label -->
                @if($katedra_id)
                <div class="absolute top-0.5 right-1 flex items-center p-2 z-10">
                    <label class="italic text-sm text-gray-400">
                        Prikaži sve
                    </label>
                    <x-checkbox
                        x-model="showAll"
                        @click="showAll = !showAll; $wire.applyFilter(showAll);"
                        class="ml-2 checkbox"
                    />
                </div>
                @endif

                <x-table :headers="$headers" :row_decoration="$row_decoration" :rows="$zaposleni_rows" class="table-sm">
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


        {{--        @if(!empty($zaposleni))--}}
{{--        <div class="w-fit shadow-lg dark:shadow-neutral" x-data="{ showInactive: false }">--}}
{{--            <x-table :headers="$headers" :row_decoration="$row_decoration" :rows="$zaposleni_rows" wire:model="zaposleni" >--}}
{{--                @scope('cell_datum_od', $zap)--}}
{{--                <x-input type="date" :value="$zap['datum_od']" wire:model="zaposleni.{{ $loop->index }}.datum_od" />--}}
{{--                @endscope--}}

{{--                @scope('cell_datum_do', $zap)--}}
{{--                <x-input type="date" :value="$zap['datum_do']" wire:model="zaposleni.{{ $loop->index }}.datum_do" />--}}
{{--                @endscope--}}

{{--                @scope('actions', $zap)--}}
{{--                <x-button icon="o-trash" wire:click="removeZaposleni({{ $loop->index }})" spinner class="btn-sm" />--}}
{{--                @endscope--}}
{{--            </x-table>--}}

{{--            <x-checkbox label="Prikaži sve" x-model="showInactive" @click="showInactive = !showInactive; $wire.applyFilter(showInactive); " right tight/>--}}
{{--        </div>--}}
{{--        @endif--}}

        <x-slot:actions>
            <x-button label="Sačuvaj" class="btn-primary" type="submit" spinner="save" />
        </x-slot:actions>

    </x-form>
</div>
