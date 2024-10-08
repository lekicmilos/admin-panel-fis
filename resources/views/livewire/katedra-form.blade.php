<div>
    <x-toast />
    <h1 class="text-3xl font-bold py-4">{{ $title }}</h1>

    <x-hr />

    <x-form wire:submit="save" class="space-y-4" no-separator>

        <!-- Naziv Input -->
        <div class="w-96">
            <x-input label="Naziv" wire:model.blur="naziv" required />
        </div>

        <!-- Sef Selection -->
        <div class="flex items-center space-x-4 flex-grow">
            <x-select label="Šef" :options="$all_zaposleni" wire:model="sef.id" placeholder="--Izaberi šefa--" required></x-select>
            <x-datepicker label="Datum od" wire:model="sef.datum_od" required icon="o-calendar" class="w-40" />
            <x-datepicker label="Datum do" wire:model="sef.datum_do" icon="o-calendar" class="w-40" />
        </div>

        <!-- Zamenik Selection -->
        <div class="flex items-center space-x-4 flex-grow">
            <x-select label="Zamenik" :options="$all_zaposleni" wire:model="zamenik.id" placeholder="--Izaberi zamenika--" required></x-select>
            <x-datepicker label="Datum od" wire:model="zamenik.datum_od" required icon="o-calendar" class="w-40" />
            <x-datepicker label="Datum do" wire:model="zamenik.datum_do" icon="o-calendar" class="w-40" />
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
            <x-button icon='o-plus' responsive label="Dodaj zaposlenog" @click="$wire.addZaposleni(selectedZaposleni)" class="btn-outline" spinner="addZaposleni"/>
        </div>

        <!-- Zaposleni Table -->
        @if(!empty($zaposleni))
            <div class="table relative w-max min-w-[40rem] m-2" x-data="{ showAll: false }">
                <!-- Container for checkbox and label -->
                @if($katedra_id)
                    <div class="absolute right-1 flex items-center p-2 z-10">
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

                <x-table
                    :headers="$headers"
                    :row_decoration="$row_decoration"
                    :rows="$zaposleni_rows"
                    class="table-xs"
                >
                    @scope('cell_datum_od', $zap)
                    <div class="relative w-40 h-12 group flex items-center justify-center">
                        <!-- Display the date as text -->
                        <span
                            x-text="new Date($wire.zaposleni[{{ $loop->index }}].datum_od).toLocaleDateString('ro-RO')"
                            class="cursor-pointer text-base">
                        </span>

                        <!-- Datepicker, initially hidden and displayed on hover -->
                        <div class="absolute inset-0 opacity-0 flex items-center justify-center group-hover:opacity-100 transition-opacity duration-300">
                            <x-datepicker
                                wire:model="zaposleni.{{ $loop->index }}.datum_od"
                                x-model="$wire.zaposleni[{{ $loop->index }}].datum_od"
                                icon="o-calendar"
                                class="w-40"
                            />
                        </div>
                    </div>
                    @endscope

                    @scope('cell_datum_do', $zap)
                    <div class="relative w-40 group flex items-center justify-center">
                        <!-- Display the date as text -->
                        <span
                            x-text="($wire.zaposleni[{{ $loop->index }}].datum_do ? new Date($wire.zaposleni[{{ $loop->index }}].datum_do).toLocaleDateString('ro-RO') : 'Nema')"
                            class="cursor-pointer text-base">
                        </span>

                        <!-- Datepicker, initially hidden and displayed on hover -->
                        <div class="absolute inset-0 opacity-0 flex items-center justify-center group-hover:opacity-100 transition-opacity duration-300">
                            <x-datepicker
                                wire:model="zaposleni.{{ $loop->index }}.datum_do"
                                x-model="$wire.zaposleni[{{ $loop->index }}].datum_do"
                                icon="o-calendar"
                                class="w-40"
                            />
                        </div>
                    </div>
                    @endscope

                    @scope('actions', $zap)
                    <x-button icon="o-trash" wire:click="removeZaposleni({{ $loop->index }})" spinner class="btn-sm" />
                    @endscope
                </x-table>
            </div>
        @endif

        <x-slot:actions>
            <x-button label="Odustani" link='/katedra' class="btn-outline" />
            <x-button label="Sačuvaj" class="btn-primary" type="submit" spinner="save" />
        </x-slot:actions>

    </x-form>
</div>
