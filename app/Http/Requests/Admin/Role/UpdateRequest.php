<?php

namespace App\Http\Requests\Admin\Role;

use App\Support\SecureRouteParameter;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
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
        $roleId = SecureRouteParameter::decode($this->route('role')) ?? $this->route('role');

        $uniqueRoleName = Rule::unique('roles', 'name')
            ->where('guard_name', config('constants.guard_name'))
            ->ignore($roleId);

        return [
            'name' => ['required', 'string', 'max:255', $uniqueRoleName],
            'parents' => 'nullable|array',
            'parents.*' => 'exists:permissions,id',
            'children' => 'nullable|array',
            'children.*' => 'exists:permissions,id',
        ];
    }
}
