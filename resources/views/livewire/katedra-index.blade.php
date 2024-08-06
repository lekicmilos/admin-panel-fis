<div class="m-8" x-data="{ deleteModal: false, deleteId: null, deleteNaziv: null}" >
    <div class="flex flex-wrap gap-5 justify-between items-center" :class="{'blur-sm': deleteModal}">
        <h1 class="text-3xl font-bold py-4">Katedre</h1>
        <div>
            <x-button label="Nova katedra" icon="o-plus" wire:click="create" class="btn-primary" />
        </div>
    </div>

    <hr class="my-5" />



    <div class="min-w-fit table-lg ">
        <x-table :headers="$headers" :rows="$katedre" link="katedra/{id}/edit" class="shadow-lg dark:shadow-neutral" with-pagination>
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
{{--                <x-modal wire:model="deleteModal" persistent>--}}
{{--                    <div class="flex text-xl mb-5">Da li ste sigurni da želite da izbrišete katedru {{ $katedra->id }} {{ $katedra->naziv_katedre }}?</div>--}}
{{--                    <x-slot:actions>--}}
{{--                        <x-button label="Otkaži" @click="$wire.deleteModal = false" />--}}
{{--                        <x-button label="Potvrdi" wire:click="deleteKatedra({{ $katedra->id }})" class="btn-primary" />--}}
{{--                    </x-slot:actions>--}}
{{--                </x-modal>--}}
{{--                <x-button icon="o-trash" @click="$wire.deleteModal = true" spinner class="btn-sm" />--}}
                <x-button
                    icon="o-trash"
                    @click="deleteModal = true; deleteId = {{ $katedra->id }}; deleteNaziv = '{{ $katedra->naziv_katedre }}'"
                    class="btn-sm" />
            </div>
            @endscope

        </x-table>
    </div>

{{--    Delete katedra modal--}}
    <template x-if="deleteModal">
        <div class="fixed inset-0 flex items-center justify-center z-50 bg-gray-800 bg-opacity-75">
            <div class="bg-base-200 p-6 rounded-xl shadow-lg">
                <div class="flex text-xl mb-5">Da li ste sigurni da želite da izbrišete katedru&nbsp;<span x-text="deleteNaziv"></span> ?</div>
                <div class="flex justify-end space-x-4">
                    <x-button label="Otkaži" @click="deleteModal = false" />
                    <x-button label="Potvrdi" wire:click="deleteKatedra(deleteId); deleteModal = false;" class="btn-primary" />
                </div>
            </div>
        </div>
    </template>
</div>
