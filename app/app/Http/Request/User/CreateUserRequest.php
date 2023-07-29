<?php

namespace App\Http\Request\User;


use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => ['required','string','max:255'],
            'email' => ['required','string','email','unique:users,email'],
            'password' => ['required','string','min:6'],
        ];
    }
}
