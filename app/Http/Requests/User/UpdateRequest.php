<?php

namespace App\Http\Requests\User;

use App\Enums\CommonStatus;
use App\Support\SecureRouteParameter;
use Illuminate\Contracts\Validation\ValidationRule;
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
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = SecureRouteParameter::decode($this->route('user')) ?? $this->route('user');
        $roleExists = Rule::exists('roles', 'id');
        $uniqueEmail = Rule::unique('users', 'email')->ignore($userId);
        $uniquePhone = Rule::unique('users', 'phone_no')->ignore($userId);

        return [
            'first_name' => 'required|string|max:50',
            'last_name' => 'nullable|string|max:50',
            'email' => ['required', 'email', $uniqueEmail],
            'phone_no' => ['required', 'digits_between:10,12', $uniquePhone],
            'address' => 'nullable|string|max:500',
            'status' => ['required', new Enum(CommonStatus::class)],
            'role' => ['required', $roleExists],
            'providers' => 'nullable|array',
            'providers.*' => 'exists:tenants,id',
        ];
    }
}
