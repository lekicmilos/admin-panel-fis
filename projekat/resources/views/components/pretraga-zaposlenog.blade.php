<select id="{{$id}}" name="{{$name}}" >
    @foreach($zaposleni as $zap)
        <option value="{{$zap->id}}">{{$zap->ime}} {{$zap->srednje_slovo}}. {{$zap->prezime}}</option>
    @endforeach
</select>