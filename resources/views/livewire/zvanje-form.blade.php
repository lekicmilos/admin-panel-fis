<div>
    <h1 class="text-3xl font-bold py-4">{{ $title }}</h1>

    <x-hr />

    <x-form wire:submit="save" class="space-y-4" no-separator>

        <!-- Naziv Input -->
        <div class="w-96">
            <x-input label="Naziv" wire:model.blur="naziv" required />
        </div>

        <!-- Naziv Input -->
        <div class="w-96">
            <x-input label="Nivo" wire:model.blur="nivo" required />
        </div>

        <x-slot:actions>
            <x-button label="Sačuvaj" class="btn-primary" type="submit" spinner="save" />
        </x-slot:actions>

    </x-form>
</div>
