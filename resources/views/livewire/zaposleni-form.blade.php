<div>
    <h1 class="text-3xl font-bold py-4">{{ $title }}</h1>

    <x-hr/>

    <x-form wire:submit="save" x-data="{ showPenzija: $wire.u_penziji }" no-separator>

        <!-- Ime, Srednje Slovo, and Prezime Inputs in the Same Row -->
        <div class="flex flex-wrap items-end space-x-4">
            <!-- Ime Input -->
            <div class="w-96">
                <x-input
                    label="Ime"
                    wire:model.blur="ime"
                    required
                />
            </div>

            <!-- Srednje Slovo Input -->
            <div class="w-16">
                <x-input
                    label="Srednje Slovo"
                    wire:model.blur="srednje_slovo"
                    required
                    class="text-center"
                />
            </div>

            <!-- Prezime Input -->
            <div class="w-96">
                <x-input
                    label="Prezime"
                    wire:model.blur="prezime"
                    required
                />
            </div>
        </div>

        <!-- Email Input -->
        <div class="w-96">
            <x-input
                label="Email"
                type="email"
                wire:model.blur="email"
                required
            />
        </div>

        <!-- Pol Input -->
        <div class="w-96">
            <x-select
                label="Pol"
                :options="$pol_options"
                wire:model="pol"
                required
                placeholder="--Izaberi pol--"
            />
        </div>

        <!-- Fis Broj Input -->
        <div class="w-96">
            <x-input
                label="Fis Broj"
                type="number"
                wire:model="fis_broj"
                required
            />
        </div>

        <!-- U Penziji Toggle -->
        <div class="w-96">
            <x-checkbox
                label="U penziji"
                wire:model="u_penziji"
                x-model="showPenzija"
            />
        </div>

        <!-- Datum Penzije Input -->
        <div class="w-96" x-show="showPenzija">
            <x-datepicker
                label="Datum penzije"
                wire:model="datum_penzije"
                icon="o-calendar"
            />
        </div>

        <!-- Katedra Selection -->
        <div class="flex flex-wrap items-center space-x-4 flex-grow">
            <div class="w-full max-w-xs">
                <x-select
                    label="Trenutno angažovanje"
                    :options="$katedra_options"
                    wire:model="katedra.id"
                    placeholder="--Izaberi katedru--"
                />
            </div>
            <x-datepicker
                label="Datum od"
                wire:model="katedra.datum_od"
                icon="o-calendar"
                class="w-40"
            />
            <x-datepicker
                label="Datum do"
                wire:model="katedra.datum_do"
                icon="o-calendar"
                class="w-40"
            />
        </div>


        @if (!empty($katedra_history))
            <label class="pb-0 label label-text font-semibold">Ostala angažovanja</label>
            <x-table
                :headers="$all_katedra_headers"
                :rows="$katedra_history"
                class="table-xs w-fit"
            >
                @scope('cell_datum_od', $zap)
                <div class="w-40  text-base">
                    {{ \Carbon\Carbon::parse($zap['datum_od'])->format('d.m.Y.') }}
                </div>
                @endscope

                @scope('cell_datum_do', $zap)
                <div class="w-40 text-base">
                    {{ $zap['datum_do'] ? \Carbon\Carbon::parse($zap['datum_do'])->format('d.m.Y.') : 'Nema' }}
                </div>
                @endscope
            </x-table>
        @endif


        <!-- Zvanje Selection -->
        <div class="mt-8 flex flex-wrap items-center space-x-4 flex-grow">
            <div class="w-full max-w-xs">
                <x-select
                    label="Trenutno zvanje"
                    :options="$zvanje_options"
                    wire:model="zvanje.id"
                    placeholder="--Izaberi zvanje--"
                />
            </div>
            <x-datepicker
                label="Datum od"
                wire:model="zvanje.datum_od"
                icon="o-calendar"
                class="w-40"
            />
            <x-datepicker
                label="Datum do"
                wire:model="zvanje.datum_do"
                icon="o-calendar"
                class="w-40"
            />
        </div>

        @if(!empty($zvanje_history))
            <label class="pb-0 label label-text font-semibold">Ostala zvanja</label>
            <x-table
                :headers="$all_zvanje_headers"
                :rows="$zvanje_history"
                class="table-xs w-fit"
            >
                @scope('cell_datum_od', $zap)
                <div class="w-40  text-base">
                    {{ \Carbon\Carbon::parse($zap['datum_od'])->format('d.m.Y.') }}
                </div>
                @endscope

                @scope('cell_datum_do', $zap)
                <div class="w-40  text-base">
                    {{ $zap['datum_do'] ? \Carbon\Carbon::parse($zap['datum_do'])->format('d.m.Y.') : 'Nema' }}
                </div>
                @endscope
            </x-table>
        @endif

        <x-slot:actions>
            <x-button label="Odustani" link='/zaposleni' class="btn-outline"/>
            <x-button label="Sačuvaj" class="btn-primary" type="submit" spinner="save"/>
        </x-slot:actions>
    </x-form>
</div>
