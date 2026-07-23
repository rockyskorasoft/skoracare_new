<?php

namespace App\Http\Requests\Package;

use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:packages,name,NULL,id,deleted_at,NULL',
            'description' => 'nullable|string|max:1000',
            'monthly_price' => 'required|numeric|min:0',
            'yearly_price' => 'required|numeric|min:0',
            'clinic_limit' => 'required|integer|min:-1',
            'user_limit' => 'required|integer|min:-1',
            'status' => 'required|string|in:active,inactive',
            'is_popular' => 'nullable|boolean',
            'permissions' => 'nullable|array',
        ];
    }
}
