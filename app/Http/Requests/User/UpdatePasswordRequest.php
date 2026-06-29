<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class UpdatePasswordRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(!auth()->user()->can('user-edit'), 403);

        return true;
    }

    public function rules()
    {
        return [
            'old_password' => ['required', function ($attribute, $value, $fail) {
                if (!Hash::check($value, auth()->user()->password)) {
                    $fail('The old password is incorrect.');
                }
            }],
            'password'         => 'required|string|min:8',
            'confirm_password' => 'required|same:password',
        ];
    }
}
