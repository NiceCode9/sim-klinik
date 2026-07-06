<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSpecializationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('master-data.specialization.edit');
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:specializations,name,'.$this->specialization->id,
        ];
    }
}
