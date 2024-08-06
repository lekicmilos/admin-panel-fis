<div x-data="{ deleteModal: false, deleteId: null, deleteNaziv: null}" >
    <div class="flex flex-wrap gap-5 justify-between items-center" :class="{'blur-sm': deleteModal}">
        <h1 class="text-3xl font-bold py-4">Katedre</h1>

        <div class="flex items-center space-x-4">
            <div class="relative flex items-center w-full max-w-xs">
            <x-input icon="o-magnifying-glass" wire:model.live.debounce="searchTerm" placeholder="Naziv..." >
            </x-input>
            <x-button
                icon="o-x-mark"
                class="btn-circle btn-ghost absolute right-3 text-gray-400 hover:text-gray-600"
                @click="$wire.set('searchTerm', '')"
                x-show="$wire.searchTerm !== ''"
            />
            </div>
            <x-button label="Nova katedra" icon="o-plus" wire:click="create" class="btn-primary" />
        </div>
    </div>

    <x-hr class="my-5" />

    <div class="min-w-fit table-lg" :class="{'blur-sm': deleteModal}">
        <x-table
            :headers="$headers"
            :rows="$katedre"
            link="katedra/{id}/edit"
            class="shadow-lg dark:shadow-neutral"
            show-empty-text empty-text="Nisu pronađene katedre."
            with-pagination>

            @scope('cell_sef', $katedra)
                {{ $katedra->sef() ?? 'Nema' }}
            @endscope
            @scope('cell_zamenik', $katedra)
                {{ $katedra->zamenik() ?? 'Nema '}}
            @endscope
            @scope('cell_broj_zap', $katedra)
                {{ $katedra->brojZaposlenih() ?? 'Nema '}}
            @endscope
            @scope('actions', $katedra)
            <div>
                <x-button
                    icon="o-trash"
                    @click="deleteModal = true; deleteId = {{ $katedra->id }}; deleteNaziv = '{{ $katedra->naziv_katedre }}'"
                    class="btn-sm bg-neutral" />
            </div>
            @endscope

        </x-table>
    </div>

{{--    Delete katedra modal--}}
    <template x-if="deleteModal">
        <div class="fixed inset-0 flex items-center justify-center z-50 bg-gray-800 bg-opacity-75">
            <div class="bg-base-200 p-6 rounded-xl shadow-lg">
                <div class="flex text-xl mb-5">Da li ste sigurni da želite da izbrišete katedru&nbsp;<strong x-text="deleteNaziv"></strong> ?</div>
                <div class="flex justify-end space-x-4">
                    <x-button label="Otkaži" @click="deleteModal = false" />
                    <x-button label="Potvrdi" wire:click="deleteKatedra(deleteId); deleteModal = false;" class="btn-primary" />
                </div>
            </div>
        </div>
    </template>
</div>
