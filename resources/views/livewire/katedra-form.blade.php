<div class="p-6 space-y-4">
    <h1 class="text-3xl font-bold py-4">{{ $title }}</h1>

    <form wire:submit.prevent="save" class="space-y-4">
        @csrf

        <!-- Naziv Input -->
        <div>
            <label for="naziv" class="block text-sm font-medium text-gray-700">Naziv</label>
            <input
                    id="naziv"
                    type="text"
                    wire:model.blur="naziv"
                    class="w-80 border rounded-md shadow-sm outline-none focus:ring-2
                    @error('naziv') border-red-500 focus:ring-red-500 focus:border-red-500
                    @else border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 @enderror"
                    required
            />
            <div class="text-sm text-red-600 mt-1">
                @error('naziv') {{ $message }} @enderror
            </div>
        </div>

        <!-- Sef Selection -->
        <div class="flex items-center space-x-4">
            <div class="flex-grow">
                <label for="sef-select" class="block text-sm font-medium text-gray-700">Šef</label>
                <div class="flex space-x-2">
                    <select id="sef-select" wire:model.blur="sef.id" class="w-48 border border-gray-300 rounded-md shadow-sm">
                        <option value="">-- Izaberi šefa --</option>
                        @foreach($all_zaposleni as $zap)
                            <option value="{{ $zap['id'] }}">{{ $zap['ime'] }}</option>
                        @endforeach
                    </select>
                    <input type="date" id="sef-datum-od" wire:model.blur="sef.datum_od" class="border border-gray-300 rounded-md shadow-sm w-32" />
                    <input type="date" id="sef-datum-do" wire:model.blur="sef.datum_do" class="border border-gray-300 rounded-md shadow-sm w-32" />
                </div>
                <div class="text-sm text-red-600 mt-1">
                    @error('sef.datum_od') {{ $message }} @enderror
                    @error('sef.datum_do') {{ $message }} @enderror
                </div>
            </div>
        </div>

        <!-- Zamenik Selection -->
        <div class="flex items-center space-x-4">
            <div class="flex-grow">
                <label for="zamenik-select" class="block text-sm font-medium text-gray-700">Zamenik</label>
                <div class="flex space-x-2">
                    <select id="zamenik-select" wire:model.live="zamenik.id" class="w-48 border border-gray-300 rounded-md shadow-sm">
                        <option value="">-- Izaberi zamenika --</option>
                        @foreach($all_zaposleni as $zap)
                            <option value="{{ $zap['id'] }}">{{ $zap['ime'] }}</option>
                        @endforeach
                    </select>
                    <input type="date" id="zamenik-datum-od" wire:model.blur="zamenik.datum_od" class="border border-gray-300 rounded-md shadow-sm w-32" />
                    <input type="date" id="zamenik-datum-do" wire:model.blur="zamenik.datum_do" class="border border-gray-300 rounded-md shadow-sm w-32" />
                </div>
                <div class="text-sm text-red-600 mt-1">
                    @error('zamenik.datum_od') {{ $message }} @enderror
                    @error('zamenik.datum_do') {{ $message }} @enderror
                </div>
            </div>
        </div>

        <div x-data="{ selectedZaposleni: '' }" class="w-96 flex items-center space-x-4">
            <div class="flex-grow">
                <label for="zaposleni-select" class="block text-sm font-medium text-gray-700">Zaposleni</label>
                <select id="zaposleni-select" x-model="selectedZaposleni" class="w-48 border border-gray-300 rounded-md shadow-sm">
                    <option value="">-- Izaberi zaposlenog --</option>
                    @foreach($all_zaposleni as $zap)
                        <option value="{{ $zap['id'] }}">{{ $zap['ime'] }}</option>
                    @endforeach
                </select>
            </div>
            <button type="button" @click="$wire.addZaposleni(selectedZaposleni)" class="bg-indigo-600 text-white px-4 py-2 rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                Dodaj zaposlenog
            </button>
        </div>


        <!-- Zaposleni Table -->
        @if(!empty($zaposleni))
        <div>
            <table class=" divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                <tr>
                    <th class="py-2 w-0"></th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-60">
                        Ime
                    </th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">
                        Datum Od
                    </th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">
                        Datum Do
                    </th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">
                        Akcija
                    </th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 ">
                @foreach($zaposleni as $index => $employee)
                    <tr wire:key="zap-el-{{ $index }}">
                        <td class="py-2 whitespace-nowrap">
                            <input type="hidden" wire:model="zaposleni.{{ $index }}.id" value="{{ $employee['id'] }}" />
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap">
                            <input type="hidden" wire:model="zaposleni.{{ $index }}.ime" value="{{ $employee['ime'] }}" />
                            {{ $employee['ime'] }}
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap">
                            <input type="date" wire:blur="validateZaposleniTable" wire:model="zaposleni.{{ $index }}.datum_od" value="{{ $employee['datum_od'] }}" class="border border-gray-300 rounded-md shadow-sm w-32" />
                            @error("zaposleni.$index.datum_od") <div class="text-sm text-red-600">{{ $message }}</div> @enderror
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap relative">
                            <input type="date" wire:blur="validateZaposleniTable" wire:model="zaposleni.{{ $index }}.datum_do" value="{{ $employee['datum_do'] }}" class="border border-gray-300 rounded-md shadow-sm w-32" />
                            @error("zaposleni.$index.datum_do") <div class="text-sm text-red-600">{{ $message }}</div> @enderror
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap">
                            <button type="button" wire:click="removeZaposleni({{ $index }})"
                                    class="bg-gray-200 text-gray-600 px-2 py-1 rounded-md shadow-sm hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400">
                                Obriši
                            </button>

                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </form>
</div>
