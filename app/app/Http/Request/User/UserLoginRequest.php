<?php

namespace App\Http\Request\User;

class UserLoginRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => ['required','string','email'],
            'password' => ['required','string'],
        ];
    }
}
