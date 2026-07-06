<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProcedureTariffRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('master-data.procedure-tariff.edit');
    }

    public function rules(): array
    {
        return [
            'icd9cm_code_id' => 'nullable|exists:icd9cm_codes,id',
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
        ];
    }
}
