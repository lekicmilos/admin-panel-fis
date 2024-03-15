<h1>Zvanja</h1>

<div>
    @if (session()->has('success'))
    <div> {{session('success')}} </div>
    @endif
</div>

<table>
    <tr>
        <th>Naziv zvanja</th>
        <th>Hijerarhijski nivo</th>
    </tr>
    @foreach ($zvanja as $zvanje)
        <tr>
            <td>{{$zvanje->naziv_zvanja}}</td>
            <td>{{$zvanje->nivo}}</td>
            <td>
                <a href="{{route('zvanje.edit', ['zvanje' => $zvanje])}}">Izmeni</a>
            </td>
            <td>
                <form method="post" action="{{route('zvanje.destroy', ['zvanje' => $zvanje])}}">
                    @csrf
                    @method('delete')
                    <input type="submit" value="Obrisi" />
                </form>
            </td>
        </tr>
    @endforeach
</table>