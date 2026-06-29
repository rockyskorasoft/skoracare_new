<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use App\Helpers\UserHelper;

class UpdateUserProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $loggedInUser = UserHelper::getLoggedInUser()->id;
        return [
            'name'          => 'required|regex:/^[A-Za-z ]+$/|max:50',
            'email'         => 'required|email|unique:users,email,' . $loggedInUser,
            'phone_no'         => 'required|digits_between:10,12|unique:users,phone_no,' . $loggedInUser,
            'profile_pic'   => 'image|nullable',
        ];
    }
}
