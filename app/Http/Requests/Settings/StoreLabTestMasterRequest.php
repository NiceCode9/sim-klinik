<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;

class StoreLabTestMasterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('master-data.lab-test.create');
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'category' => 'required|in:lab,radiology',
            'unit' => 'nullable|string|max:50',
            'normal_range_min' => 'nullable|numeric',
            'normal_range_max' => 'nullable|numeric',
            'tariff_id' => 'nullable|exists:tariffs,id',
        ];
    }
}
