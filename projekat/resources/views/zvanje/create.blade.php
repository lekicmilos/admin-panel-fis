<h1>Novo zvanje</h1>

<div>
    @if($errors->any())
    <ul>
        @foreach($errors->all() as $error)
        <li>{{$error}}</li>
        @endforeach
    </ul>

    @endif
</div>

<form action="{{route('zvanje.store')}}" method="post">

    @csrf
    @method('post')

    <div>
        <label for="naziv">Naziv</label>
        <input id="naziv" type="text" name="naziv" />
    </div>

    <div>
        <label for="nivo">Hijerarhijski nivo</label>
        <input id="nivo" type="text" name="nivo"  />
    </div>

    <div>
        <input type="submit" value="Sačuvaj" />
    </div>

</form>
