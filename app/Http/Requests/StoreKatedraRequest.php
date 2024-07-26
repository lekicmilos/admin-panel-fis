<?php

namespace App\Http\Requests;

use App\DTO\KatedraDTO;
use App\DTO\ZaposleniNaKatedriDTO;
use App\Models\Zaposleni;
use Illuminate\Foundation\Http\FormRequest;

class StoreKatedraRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->exists('zaposleni') && is_array($this->get('zaposleni')))
        {
            $this->merge([
                'zaposleni_ids' => array_keys($this->get('zaposleni'))
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'naziv' => 'required|string|min:3',
            'zaposleni' => 'required|array',
            'zaposleni_ids'=> 'exists:zaposleni,id',
            'zaposleni.*.datum_od' => 'required|date',
            'zaposleni.*.datum_do' => 'nullable|date|after:zaposleni.*.datum_od',
            'sef_id' => 'exists:zaposleni,id',
            'sef_datum_od' => 'required|date',
            'sef_datum_do' => 'nullable|date|after:sef_datum_od',
            'zamenik_id' => 'exists:zaposleni,id',
            'zamenik_datum_od' => 'required|date',
            'zamenik_datum_do' => 'nullable|date|after:zamenik_datum_od',
        ];
    }

    public function toDTO($katedra_id = null): KatedraDTO
    {
        $sef = new ZaposleniNaKatedriDTO($this->sef_id,null,$this->sef_datum_od,$this->sef_datum_do);

        $zamenik = new ZaposleniNaKatedriDTO($this->zamenik_id,null,$this->zamenik_datum_od,$this->zamenik_datum_do);

        $zaposleni = [];
        foreach ($this->zaposleni as $zap_id => $zap)
        {
            $zaposleni[] = new ZaposleniNaKatedriDTO(
                id: $zap_id,
                ime: null,
                datum_od: $zap['datum_od'],
                datum_do: $zap['datum_do']
            );
        }

        $katedraDTO = new KatedraDTO(
            id: $katedra_id,
            naziv: $this->naziv,
            aktivna: true,
            zaposleni: $zaposleni,
            sef: $sef,
            zamenik: $zamenik
        );

        return $katedraDTO;
    }
}
