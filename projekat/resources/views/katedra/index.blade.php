<h1>Katedre</h1>

<div>
    @if (session()->has('success'))
        <div> {{session('success')}} </div>
    @endif
</div>

<table>
    <tr>
        <th>Naziv</th>
        <th>Šef</th>
        <th>Zamenik</th>

    </tr>
    @foreach ($katedre as $katedra)
        <tr>
            <td>{{$katedra->naziv_katedre}}</td>
            <td>{{$katedra->sef() ?? 'Nema'}}</td>
            <td>{{$katedra->zamenik() ?? 'Nema'}}</td>
            <td>
                <a href="{{route('katedra.edit', ['katedra_id' => $katedra->id])}}">Izmeni</a>
            </td>
{{--            <td>--}}
{{--                <form method="post" action="{{route('zvanje.destroy', ['zvanje' => $zvanje])}}">--}}
{{--                    @csrf--}}
{{--                    @method('delete')--}}
{{--                    <input type="submit" value="Obrisi" />--}}
{{--                </form>--}}
{{--            </td>--}}
        </tr>
    @endforeach
</table>