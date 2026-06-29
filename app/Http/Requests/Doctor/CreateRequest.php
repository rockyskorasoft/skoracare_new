<?php

namespace App\Http\Requests\Doctor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:50',
            'last_name' => 'nullable|string|max:50',
            'email' => ['required', 'email', Rule::unique('users', 'email')->whereNull('deleted_at')],
            'phone_no' => ['nullable', 'digits_between:10,12', Rule::unique('users', 'phone_no')->whereNull('deleted_at')],
            'status' => 'required|string',
            'qualification' => 'nullable|string|max:100',
            'registration_number' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:500',
        ];
    }
}
