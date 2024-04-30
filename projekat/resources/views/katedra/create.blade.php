<head>
    @if($method=='post')
        @php($title = "Nova katedra")
    @else
        @php($title = "Izmeni katedru")
    @endif

    <title>{{$title}}</title>

{{--        kako importovati javascript bukvalno nemam pojma--}}
    <script type="text/javascript" src="{{asset('js/katedra-create-table.js')}}"></script>
</head>

<body>

    <h1>{{$title}}</h1>

    <div>
        @if($errors->any())
        <ul>
            @foreach($errors->all() as $error)
            <li>{{$error}}</li>
            @endforeach
        </ul>
        @endif
    </div>

    <form action="{{$method=='post' ? route('katedra.store') : route('katedra.update', ['katedra_id' => $katedra->id])}}" method="post">

        @csrf
        @method($method)

        <div>
            <label for="naziv">Naziv</label>
            <input id="naziv" type="text" name="naziv" value="{{$katedra->naziv??''}}" required />
        </div>

        <div>
            <label>Pretraži zaposlenog</label>
            <x-pretraga-zaposlenog id="pretraga-zap" name="" :zaposleni="$zaposleni" :selected="null"></x-pretraga-zaposlenog>

            <button id="dodaj-zap" type="button">Dodaj zaposlenog</button>

            <div class="tabela-zap">
                <table>
                    <tr>
                        <th>Zaposleni</th>
                        <th>Datum angažovanja</th>
                        <th>Datum završetka angažovanja</th>
                    </tr>

                    <tbody id="tbody">

                    @if($katedra)
                    @foreach($katedra->zaposleni as $zap)
                        <tr class="rowClass">
                            <td class="">
                                {{$zap->ime}}
                            </td>

                            <td class="">
                                <input name="zaposleni[{{$zap->id}}][datum_od]" id="datum-od" type="date" value="{{$zap->datum_od}}" required/>
                            </td>

                            <td class="">
                                <input name="zaposleni[{{$zap->id}}][datum_do]" id="datum-do" type="date" value="{{$zap->datum_do}}"/>
                            </td>

                            <td class="">
                                <button class="remove" type="button" id="{{$zap->id}}">Obriši</button>
                            </td>
                        </tr>
                    @endforeach
                    @endif
                    </tbody>

                </table>
            </div>
        </div>

        <div class="sef-katedre">
            <table>
                <tr>
                    <th>Šef</th>
                    <th>Datum angažovanja</th>
                    <th>Datum završetka angažovanja</th>
                </tr>

                <tr>
                    <td class="">
                        <x-pretraga-zaposlenog id="pretraga-sef" name="sef_id" :zaposleni="$zaposleni" :selected="$katedra->sef??null"></x-pretraga-zaposlenog>
                    </td>

                    <td class="">
                        <input name="sef_datum_od" id="datum-od-sef" type="date" value="{{$katedra->sef->datum_od??''}}" required/>
                    </td>

                    <td class="">
                        <input name="sef_datum_do" id="datum-do-sef" type="date" value="{{$katedra->sef->datum_do??''}}"/>
                    </td>
                </tr>
            </table>
        </div>

        <div class="zamenik-katedre">
            <table>
                <tr>
                    <th>Zamenik</th>
                    <th>Datum angažovanja</th>
                    <th>Datum završetka angažovanja</th>
                </tr>

                <tr>
                    <td class="">
                        <x-pretraga-zaposlenog id="pretraga-zamenik" name="zamenik_id" :zaposleni="$zaposleni" :selected="$katedra->zamenik??null"></x-pretraga-zaposlenog>
                    </td>

                    <td class="">
                        <input name="zamenik_datum_od" id="datum-od-zamenik" type="date" value="{{$katedra->zamenik->datum_od??''}}" required/>
                    </td>

                    <td class="">
                        <input name="zamenik_datum_do" id="datum-do-zamenik" type="date" value="{{$katedra->zamenik->datum_do??''}}"/>
                    </td>
                </tr>
            </table>
        </div>

        <div>
            <input type="submit" value="Sačuvaj" />
        </div>

    </form>

</body>

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
                    <td class="">
                        ${zap_ime}
                    </td>

                    <td class="">
                        <input name="zaposleni[${zap_id}][datum_od]" id="datum-od" type="date" value="" required/>
                    </td>

                    <td class="">
                        <input name="zaposleni[${zap_id}][datum_do]" id="datum-do" type="date" value=""/>
                    </td>

                    <td class="">
                        <button class="remove" type="button" id="${zap_id}">Obrisi</button>
                    </td>
                </tr>`;

                // Dodavanje reda u tabelu
                $('#tbody').append(dynamicRowHTML);
            }
        });

        // Brisanje reda na dugme
        $('#tbody').on('click', '.remove', function () {
            // brisanje idja iz liste
            const deleted_id = $(this).id;
            zap_ids.splice(zap_ids.indexOf(deleted_id), 1);

            // uklanjanje reda
            $(this).parent('td').parent('tr.rowClass').remove();

        });
    })

</script>