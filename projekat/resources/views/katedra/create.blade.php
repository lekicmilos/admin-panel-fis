<h1>Nova katedra</h1>

<div>
    @if($errors->any())
    <ul>
        @foreach($errors->all() as $error)
        <li>{{$error}}</li>
        @endforeach
    </ul>

    @endif
</div>

<form action="{{route('katedra.store')}}" method="post">

    @csrf
    @method('post')

    <div>
        <label for="naziv">Naziv</label>
        <input id="naziv" type="text" name="naziv" required />
    </div>

    <div>
        <label for="pretraga-zap">Pretraži zaposlenog</label>
        <select id="pretraga-zap" name="pretraga-zap" >
            @foreach($zaposleni as $zap)
                <option value="{{$zap->id}}">{{$zap->ime}} {{$zap->srednje_slovo}}. {{$zap->prezime}}</option>
            @endforeach

        </select>
        <button id="dodaj-zap" type="button">Dodaj zaposlenog</button>

        <div class="tabela-zap">
            <table>
                <tr>
                    <th>Zaposleni</th>
                    <th>Datum angažovanja</th>
                    <th>Datum završetka angažovanja</th>
                </tr>

                <tbody id="tbody">

                </tbody>

            </table>
        </div>
    </div>

    <div>
        <input type="submit" value="Sačuvaj" />
    </div>

</form>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    $(document).ready(() => {

        let zap_ids=[];

        // Dodavanje novog zaposlenog na klik
        $('#dodaj-zap').click(function () {
            let selected = $('#pretraga-zap').find(':selected');
            let zap_id = selected.val();
            let zap_ime = selected.text();

            // provera da li je vec unet
            if (zap_ids.includes(zap_id)) {
                alert("Zaposleni je već dodat");
            } else {
                zap_ids.push(zap_id);

                let dynamicRowHTML = `
                <tr class="rowClass">
                    <td class="zap-id">
                        <input name="zaposleni_id[]" type="hidden" value=${zap_id} />
                    </td>

                    <td class="">
                        ${zap_ime}
                    </td>

                    <td class="">
                        <input name="datum_od[]" id="datum-od" type="date" value="" required/>
                    </td>

                    <td class="">
                        <input name="datum_do[]" id="datum-do" type="date" value=""/>
                    </td>

                    <td class="">
                        <button class="remove" type="button">Obrisi
                        </button>
                    </td>
                </tr>`;

                // Dodavanje reda u tabelu
                $('#tbody').append(dynamicRowHTML);
            }
        });

        // Brisanje reda na dugme
        $('#tbody').on('click', '.remove', function () {
            const deleted_id = $(this).parent('td').siblings('td.zap-id').children('input')[0].value;
            zap_ids.splice(zap_ids.indexOf(deleted_id), 1);
            $(this).parent('td').parent('tr.rowClass').remove();

        });
    })

</script>