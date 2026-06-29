<?php

namespace App\Http\Requests\Auth;

use App\Enums\UserType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\Password;

/**
 * SignupRequest
 *
 * Validates the self-registration form data (both steps combined on submit).
 * Step 1 fields: first_name, last_name, email, phone_no
 * Step 2 fields: password, password_confirmation, user_type
 */
class SignupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            /* ── Step 1 ── */
            'first_name' => ['required', 'string', 'max:100'],
            'last_name'  => ['nullable', 'string', 'max:100'],
            'email'      => ['required', 'email', 'max:191', 'unique:users,email'],
            'phone_no'   => ['required', 'string', 'max:20', 'unique:users,phone_no'],

            /* ── Step 2 ── */
            'password'   => ['required', 'string', 'min:8', 'confirmed', Password::defaults()],
            'user_type'  => ['required', new Enum(UserType::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => __('validation.required', ['attribute' => __('labels.first_name')]),
            'email.required'      => __('validation.required', ['attribute' => __('labels.email')]),
            'email.unique'        => __('validation.unique',   ['attribute' => __('labels.email')]),
            'phone_no.required'   => __('validation.required', ['attribute' => __('labels.mobile_number')]),
            'phone_no.unique'     => __('validation.unique',   ['attribute' => __('labels.mobile_number')]),
            'password.required'   => __('validation.required', ['attribute' => __('labels.password')]),
            'password.confirmed'  => __('validation.confirmed', ['attribute' => __('labels.password')]),
            'user_type.required'  => __('validation.required', ['attribute' => __('labels.i_am_a')]),
        ];
    }
}
