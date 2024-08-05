<div class="p-6 space-y-4">
    <h1 class="text-3xl font-bold py-4">{{ $title }}</h1>

    <x-form wire:submit="save" class="space-y-4" no-separator>
        @csrf

        <!-- Naziv Input -->
        <div class="w-80">
            <x-input
                    id="naziv"
                    type="text"
                    label="Naziv"
                    wire:model.blur="naziv"
                    required
            />
        </div>

        {{--   POGLEDAJ OVO     https://mary-ui.com/docs/components/choices#searchable-server--}}
        <!-- Sef Selection -->
        <div class="flex items-center space-x-4">
            <div class="flex-grow">
                <div class="flex space-x-2">
                    <x-select label="Šef" :options="$all_zaposleni" wire:model="sef.id" placeholder="--Izaberi šefa--" required></x-select>
                    <x-input label="Datum od" type="date" wire:model="sef.datum_od" required/>
                    <x-input label="Datum do" type="date" wire:model="sef.datum_do" />
                </div>
{{--                <div class="text-sm text-red-600 mt-1">--}}
{{--                    @error('sef.datum_od') {{ $message }} @enderror--}}
{{--                    @error('sef.datum_do') {{ $message }} @enderror--}}
{{--                </div>--}}
            </div>
        </div>

        <!-- Zamenik Selection -->
        <div class="flex items-center space-x-4">
            <div class="flex-grow">
                <div class="flex space-x-2">
                    <x-select label="Zamenik" :options="$all_zaposleni" wire:model="zamenik.id" placeholder="--Izaberi zamenika--" required></x-select>
                    <x-input label="Datum od" type="date" wire:model="zamenik.datum_od" required />
                    <x-input label="Datum do" type="date" wire:model="zamenik.datum_do"/>
                </div>
{{--                <div class="text-sm text-red-600 mt-1">--}}
{{--                    @error('zamenik.datum_od') {{ $message }} @enderror--}}
{{--                    @error('zamenik.datum_do') {{ $message }} @enderror--}}
{{--                </div>--}}
            </div>
        </div>

        <div x-data="{ selectedZaposleni: '' }" class="w-fit flex items-end space-x-4">
            <div class="flex-grow">
                <x-select label="Zaposleni" :options="$all_zaposleni" x-model="selectedZaposleni" placeholder="--Izaberi zaposlenog--"></x-select>
            </div>
            <x-button icon='o-plus' label="Dodaj zaposlenog" @click="$wire.addZaposleni(selectedZaposleni)" class="btn-outline" spinner="addZaposleni"/>
        </div>

        <!-- Zaposleni Table -->
        @if(!empty($zaposleni))
        <div class="w-fit">
            <x-table :headers="$headers" :rows="$zaposleni" wire:model="zaposleni">
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

{{--        @if(!empty($zaposleni))--}}
{{--        <div>--}}
{{--            <table class=" divide-y divide-gray-200 text-sm">--}}
{{--                <thead class="bg-gray-50">--}}
{{--                <tr>--}}
{{--                    <th class="py-2 w-0"></th>--}}
{{--                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-60">--}}
{{--                        Ime--}}
{{--                    </th>--}}
{{--                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">--}}
{{--                        Datum Od--}}
{{--                    </th>--}}
{{--                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">--}}
{{--                        Datum Do--}}
{{--                    </th>--}}
{{--                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">--}}
{{--                        Akcija--}}
{{--                    </th>--}}
{{--                </tr>--}}
{{--                </thead>--}}
{{--                <tbody class="bg-white divide-y divide-gray-200 ">--}}
{{--                @foreach($zaposleni as $index => $employee)--}}
{{--                    <tr wire:key="zap-el-{{ $index }}">--}}
{{--                        <td class="py-2 whitespace-nowrap">--}}
{{--                            <input type="hidden" wire:model="zaposleni.{{ $index }}.id" value="{{ $employee['id'] }}" />--}}
{{--                        </td>--}}
{{--                        <td class="px-4 py-2 whitespace-nowrap">--}}
{{--                            <input type="hidden" wire:model="zaposleni.{{ $index }}.ime" value="{{ $employee['ime'] }}" />--}}
{{--                            {{ $employee['ime'] }}--}}
{{--                        </td>--}}
{{--                        <td class="px-4 py-2 whitespace-nowrap">--}}
{{--                            <input type="date" wire:blur="validateZaposleniTable" wire:model="zaposleni.{{ $index }}.datum_od" value="{{ $employee['datum_od'] }}" class="border border-gray-300 rounded-md shadow-sm w-32" />--}}
{{--                            @error("zaposleni.$index.datum_od") <div class="text-sm text-red-600">{{ $message }}</div> @enderror--}}
{{--                        </td>--}}
{{--                        <td class="px-4 py-2 whitespace-nowrap relative">--}}
{{--                            <input type="date" wire:blur="validateZaposleniTable" wire:model="zaposleni.{{ $index }}.datum_do" value="{{ $employee['datum_do'] }}" class="border border-gray-300 rounded-md shadow-sm w-32" />--}}
{{--                            @error("zaposleni.$index.datum_do") <div class="text-sm text-red-600">{{ $message }}</div> @enderror--}}
{{--                        </td>--}}
{{--                        <td class="px-4 py-2 whitespace-nowrap">--}}
{{--                            <button type="button" wire:click="removeZaposleni({{ $index }})"--}}
{{--                                    class="bg-gray-200 text-gray-600 px-2 py-1 rounded-md shadow-sm hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400">--}}
{{--                                Obriši--}}
{{--                            </button>--}}

{{--                        </td>--}}
{{--                    </tr>--}}
{{--                @endforeach--}}
{{--                </tbody>--}}
{{--            </table>--}}
{{--        </div>--}}
{{--        @endif--}}
    </x-form>
</div>
