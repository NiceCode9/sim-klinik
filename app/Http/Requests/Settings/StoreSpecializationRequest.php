<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;

class StoreSpecializationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('master-data.specialization.create');
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:specializations,name',
        ];
    }
}
