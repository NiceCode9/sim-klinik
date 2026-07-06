<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('master-data.employee.edit');
    }

    public function rules(): array
    {
        return [
            'user_id' => 'nullable|exists:users,id',
            'name' => 'required|string|max:255',
            'employee_type' => 'required|string|in:dokter,perawat,apoteker,kasir,analis,radiografer,resepsionis',
            'specialization_id' => 'nullable|exists:specializations,id',
            'str_number' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ];
    }
}
