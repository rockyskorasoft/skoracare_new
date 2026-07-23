<?php

namespace App\Http\Requests\Staff;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:50',
            'last_name' => 'nullable|string|max:50',
            'email' => ['required', 'email', Rule::unique('users', 'email')->whereNull('deleted_at')],
            'phone_no' => ['nullable', 'digits_between:10,12', Rule::unique('users', 'phone_no')->whereNull('deleted_at')],
            'password' => 'required|string|min:6',
            'status' => 'required|string|in:active,inactive',
            'role' => 'nullable|string|exists:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
            'doctor_id' => 'nullable|exists:users,id',
            'clinic_ids' => 'nullable|array',
            'clinic_ids.*' => 'exists:clinics,id',
        ];
    }
}
