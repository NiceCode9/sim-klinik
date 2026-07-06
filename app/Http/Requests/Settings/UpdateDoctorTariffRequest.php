<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDoctorTariffRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('master-data.doctor-tariff.edit');
    }

    public function rules(): array
    {
        return [
            'employee_id' => 'required|exists:employees,id',
            'specialization_id' => 'nullable|exists:specializations,id',
            'amount' => 'required|numeric|min:0',
        ];
    }
}
