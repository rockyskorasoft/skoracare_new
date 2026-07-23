<?php

namespace App\Http\Requests\Package;

use App\Support\SecureRouteParameter;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $packageId = SecureRouteParameter::decode($this->route('package')) ?? $this->route('package');

        return [
            'name' => 'required|string|max:255|unique:packages,name,' . $packageId . ',id,deleted_at,NULL',
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
