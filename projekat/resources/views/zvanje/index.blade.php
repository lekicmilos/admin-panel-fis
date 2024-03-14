<h1>Zvanja</h1>

<table>
    <tr>
        <th>Naziv zvanja</th>
        <th>Hijerarhijski nivo</th>
    </tr>
    @foreach ($zvanja as $zvanje)
        <tr>
            <td>{{$zvanje->naziv_zvanja}}</td>
            <td>{{$zvanje->nivo}}</td>
        </tr>
    @endforeach
</table>