<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;

class StoreDrugRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('master-data.drug.create');
    }

    public function rules(): array
    {
        return [
            'code' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'unit' => 'required|string|max:50',
            'is_fractional' => 'boolean',
            'pricing_type' => 'required|in:margin_percentage,flat',
            'price_value' => 'required|numeric|min:0',
            'minimum_stock' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ];
    }
}
