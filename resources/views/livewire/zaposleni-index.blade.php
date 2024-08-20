<div>
    <div class="flex flex-wrap gap-5 justify-between items-center" :class="{'blur-sm': deleteModal}">
        <h1 class="text-4xl font-bold py-4">Zaposleni</h1>

        <div class="flex items-center space-x-4">
            <div class="relative flex items-center w-full max-w-xs">
                <x-input
                    icon="o-magnifying-glass"
                    wire:model.live.debounce="search.ime"
                    placeholder="Ime..."
                    clearable
                />
            </div>
            <x-button label="Filteri" @click="$wire.drawer = true" responsive icon="o-funnel"/>
            <x-button label="Novi zaposleni" responsive icon="o-plus" link="/zaposleni/create" class="btn-primary"/>
        </div>
    </div>

    <x-hr class="my-5"/>

    <x-table
        :headers="$headers"
        :rows="$zaposleni"
        link="zaposleni/{id}/edit"
        class="shadow-lg dark:shadow-neutral table-lg"
        show-empty-text empty-text="Nisu pronađeni zaposleni."
        :sort-by="$sortBy"
        with-pagination
    >
        @scope('cell_ime', $zap)
        {{ $zap->punoIme() ?? 'Nema' }}
        @endscope

        @scope('cell_katedra', $zap)
        <div x-data="{ katedra: `{{$zap->katedra->first()?->naziv_katedre ?? 'Nema' }}`}">
            <span :class="{ 'text-red-500' : katedra === 'Nema' }" x-text="katedra"></span>
        </div>
        @endscope

        @scope('cell_zvanje', $zap)
        <div x-data="{ zvanje: `{{$zap->zvanje->first()?->naziv_zvanja ?? 'Nema' }}`}">
            <span :class="{ 'text-red-500' : zvanje === 'Nema' }" x-text="zvanje"></span>
        </div>
        @endscope

        @scope('actions', $zap)
        <x-button icon="o-trash" spinner class="btn-ghost btn-sm"/>
        @endscope
    </x-table>

    <!-- FILTER DRAWER -->
    <x-drawer
        wire:model="drawer"
        title="Filteri"
        right separator with-close-button close-on-escape
        class="lg:w-1/3"
    >
        <div class="space-y-4">
            <x-input
                label="Puno ime ili email"
                placeholder="Ime..."
                wire:model.live.debounce="search.ime"
                icon="o-magnifying-glass"
                clearable
                @keydown.enter="$wire.drawer = false"
            />

            <x-input
                label="FIS broj"
                type="number"
                placeholder="..."
                wire:model.live.debounce="search.fis"
                icon="o-magnifying-glass"
                clearable
                @keydown.enter="$wire.drawer = false"
            />

            <x-select
                label="Katedra"
                :options="$katedra_options"
                wire:model.live="search.katedra"
                placeholder="--Izaberi katedru--"
            />

            <x-select
                label="Zvanje"
                :options="$zvanje_options"
                wire:model.live="search.zvanje"
                placeholder="--Izaberi zvanje--"
            />
        </div>

        <x-slot:actions>
            <div class="flex space-x-2 p-4">
                <x-button label="Resetuj" icon="o-x-mark" @click="$wire.clearFilters(); $wire.drawer = false;" spinner/>
                <x-button label="Gotovo" icon="o-check" class="btn-primary" @click="$wire.drawer = false"/>
            </div>
        </x-slot:actions>
    </x-drawer>

</div>
