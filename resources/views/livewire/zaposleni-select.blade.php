<div>
    <select id="zaposleni-select" wire:model.live="selectedId" class="border border-gray-300 rounded-md shadow-sm">
        <option value="">-- Izaberi zaposlenog --</option>
        @foreach($zaposleni as $zap)
            <option value="{{ $zap['id'] }}">{{ $zap['ime'] }}</option>
        @endforeach
    </select>
</div>