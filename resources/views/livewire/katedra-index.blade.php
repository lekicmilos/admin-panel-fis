<div x-data="{ deleteModal: false, deleteId: null, deleteNaziv: null}">
    <x-toast/>
    <div class="flex flex-wrap gap-5 justify-between items-center" :class="{'blur-sm': deleteModal}">
        <h1 class="text-4xl font-bold py-4">Katedre</h1>

        <div class="flex items-center space-x-4">
            <div class="relative flex items-center w-full max-w-xs">
                <x-input icon="o-magnifying-glass" wire:model.live.debounce="searchTerm" placeholder="Naziv..."/>
                <x-button
                    icon="o-x-mark"
                    class="btn-circle btn-ghost absolute right-3 text-gray-400 hover:text-gray-600"
                    @click="$wire.set('searchTerm', '')"
                    x-show="$wire.searchTerm !== ''"
                />
            </div>
            <x-button label="Nova katedra" responsive icon="o-plus" link="/katedra/create" class="btn-primary"/>
        </div>
    </div>

    <x-hr class="my-5"/>

    <div class="max-w-fit mx-auto table-lg" :class="{'blur-sm': deleteModal}">
        <x-table
            :headers="$headers"
            :rows="$katedre"
            link="katedra/{id}/edit"
            class="shadow-lg dark:shadow-neutral"
            show-empty-text empty-text="Nisu pronađene katedre."
            with-pagination>

            @scope('cell_sef', $katedra)
            <div x-data="{ sefName: `{{ $katedra->sef->first()?->punoIme() ?? 'Nema' }}`}" >
                <span :class="{ 'text-red-500' : sefName === 'Nema' }" x-text="sefName"></span>
            </div>
            @endscope

            @scope('cell_zamenik', $katedra)
            <div x-data="{ zamName: `{{ $katedra->zamenik->first()?->punoIme() ?? 'Nema' }}`}" >
                <span :class="{ 'text-red-500' : zamName === 'Nema' }" x-text="zamName"></span>
            </div>
            @endscope

            @scope('cell_broj_zap', $katedra)
            <div x-data="{ brZap: {{ $katedra->aktivni_zaposleni_count }} }" >
                <span :class="{ 'text-red-500' : brZap == 0 }" x-text="brZap"></span>
            </div>
            @endscope

            @scope('actions', $katedra)
            <x-button
                icon="o-trash"
                @click="deleteModal = true; deleteId = {{ $katedra->id }}; deleteNaziv = '{{ $katedra->naziv_katedre }}'"
                class="btn-sm btn-ghost"
            />
            @endscope

        </x-table>
    </div>

    {{--    Delete katedra modal--}}
    <template x-if="deleteModal">
        <div class="fixed inset-0 flex items-center justify-center z-50 bg-gray-800 bg-opacity-75">
            <div class="bg-base-200 p-6 rounded-xl shadow-lg">
                <div class="flex text-xl mb-5">Da li ste sigurni da želite da izbrišete katedru&nbsp;<strong
                        x-text="deleteNaziv"></strong> ?
                </div>
                <div class="flex justify-end space-x-4">
                    <x-button label="Otkaži" @click="deleteModal = false"/>
                    <x-button label="Potvrdi" wire:click="deleteKatedra(deleteId); deleteModal = false;"
                              class="btn-primary"/>
                </div>
            </div>
        </div>
    </template>
</div>
