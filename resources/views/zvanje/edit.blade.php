<h1>Izmeni zvanje</h1>

<div>
    @if($errors->any())
    <ul>
        @foreach($errors->all() as $error)
        <li>{{$error}}</li>
        @endforeach
    </ul>

    @endif
</div>

<form action="{{route('zvanje.update', ['zvanje' => $zvanje])}}" method="post">

    @csrf
    @method('put')

    <div>
        <label for="naziv">Naziv</label>
        <input id="naziv" type="text" name="naziv" value="{{$zvanje->naziv_zvanja}}"/>
    </div>

    <div>
        <label for="nivo">Hijerarhijski nivo</label>
        <input id="nivo" type="text" name="nivo" value="{{$zvanje->nivo}}" />
    </div>

    <div>
        <input type="submit" value="Izmeni" />
    </div>

</form>
