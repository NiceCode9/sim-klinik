<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('settings.role.edit');
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:roles,name,'.$this->role->id,
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name',
        ];
    }
}
