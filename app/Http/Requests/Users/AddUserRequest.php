<?php

namespace App\Http\Requests\Users;

use App\Http\Requests\Request;

class AddUserRequest extends Request
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
     * @return array
     */
    public function rules()
    {
        return [
            'karyawan'  => 'required',
            'username'  => 'required|unique:users',
            'password'  => 'required',
            'levels'    => 'required'
        ];
    }
}
