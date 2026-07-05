<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;

class StoreMenuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('settings.menu.create');
    }

    public function rules(): array
    {
        return [
            'parent_id' => 'nullable|exists:menus,id',
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'route_name' => 'nullable|string|max:255',
            'permission_name' => 'nullable|string|max:255|exists:permissions,name',
            'order' => 'integer|min:0',
            'is_active' => 'boolean',
        ];
    }
}
