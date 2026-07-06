<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;

class StoreTariffRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('master-data.tariff.create');
    }

    public function rules(): array
    {
        return [
            'tariff_type' => 'required|string|in:tuslah,embalase,procedure,doctor_fee,other',
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ];
    }
}
