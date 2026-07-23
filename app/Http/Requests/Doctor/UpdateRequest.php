<?php

namespace App\Http\Requests\Doctor;

use App\Enums\CommonStatus;
use App\Support\SecureRouteParameter;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class UpdateRequest extends FormRequest
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
        $doctorId = SecureRouteParameter::decode($this->route('doctor')) ?? $this->route('doctor');

        return [
            'first_name' => 'required|string|max:50',
            'last_name' => 'nullable|string|max:50',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($doctorId)->whereNull('deleted_at')],
            'phone_no' => ['nullable', 'digits_between:10,12', Rule::unique('users', 'phone_no')->ignore($doctorId)->whereNull('deleted_at')],
            'status' => ['required', new Enum(CommonStatus::class)],
            'qualification' => 'nullable|string|max:100',
            'registration_number' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:500',
            'package_id' => 'nullable|exists:packages,id',
            'max_clinics' => 'nullable|integer|min:-1',
            'max_users' => 'nullable|integer|min:-1',
        ];
    }
}
