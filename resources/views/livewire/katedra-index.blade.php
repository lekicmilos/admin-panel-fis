<div class="m-8">
    <div class="flex flex-wrap gap-5 justify-between items-center">
        <h1 class="text-3xl font-bold py-4">Katedre</h1>
        <div>
            <x-button label="Nova katedra" icon="o-plus" wire:click="create" class="btn-primary" />
        </div>
    </div>

    <hr class="my-5" />



    <div class="min-w-fit table-lg shadow-lg dark:shadow-neutral">
        <x-table :headers="$headers" :rows="$katedre" link="katedra/{id}/edit">
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
            <div class="flex ">
                <x-modal wire:model="deleteModal" persistent>
                    <div class="flex text-xl mb-5">Da li ste sigurni da želite da izbrišete katedru?</div>
                    <x-slot:actions>
                        <x-button label="Otkaži" @click="$wire.deleteModal = false" />
                        <x-button label="Potvrdi" wire:click="deleteKatedra({{ $katedra->id }})" class="btn-primary" />
                    </x-slot:actions>
                </x-modal>
                <x-button icon="o-trash" @click="$wire.deleteModal = true" spinner class="btn-sm" />
            </div>
            @endscope

        </x-table>
    </div>
</div>
