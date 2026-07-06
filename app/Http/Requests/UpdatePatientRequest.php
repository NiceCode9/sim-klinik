<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePatientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nik' => 'nullable|string|max:20|unique:patients,nik,'.$this->patient->id,
            'name' => 'required|string|max:255',
            'gender' => 'required|in:L,P',
            'birth_date' => 'required|date',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'blood_type' => 'nullable|string|max:5',
            'allergies' => 'nullable|string',
        ];
    }
}
