<?php

namespace App\Http\Requests\Admin\Role;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $uniqueRoleName = Rule::unique('roles', 'name')
            ->where('guard_name', config('constants.guard_name'));

        return [
            'name' => ['required', 'string', 'max:255', $uniqueRoleName],
            'parents' => 'nullable|array',
            'parents.*' => 'exists:permissions,id',
            'children' => 'nullable|array',
            'children.*' => 'exists:permissions,id',
        ];
    }
}
