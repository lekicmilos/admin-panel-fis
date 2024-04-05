<select id="{{$id}}" name="{{$name}}">
    @foreach($zaposleni as $zap)
        <option value="{{$zap->id}}" {{ ($selected && $zap->id === $selected->id) ? 'selected' : '' }}>
                {{$zap->ime}} {{$zap->srednje_slovo}}. {{$zap->prezime}}
        </option>
    @endforeach
</select>