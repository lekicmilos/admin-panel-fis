@extends('layouts.app')

<div>

    <h1>Katedre</h1>

    <form action="{{route('katedra.search')}}" method="get">
        <input type="text" name="search" placeholder="Pretraži katedre">
        <button type="submit">Pretraži</button>
    </form>

    <div>
        @if (session()->has('success'))
            <div> {{session('success')}} </div>
        @endif
    </div>

</div>

@if (count($katedre) > 0)
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
                <td>
                    <form method="post" action="{{route('katedra.destroy', ['katedra_id' => $katedra->id])}}">
                        @csrf
                        @method('delete')
                        <input type="submit" value="Obriši" />
                    </form>
                </td>
            </tr>
        @endforeach
    </table>
@else
    <p>Nije pronađena katedra.</p>
@endif