<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreKatedraRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
//    public function authorize(): bool
//    {
//        return false;
//    }

    protected function prepareForValidation()
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
}
