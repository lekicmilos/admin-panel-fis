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