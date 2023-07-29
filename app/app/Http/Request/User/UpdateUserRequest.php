<?php

namespace App\Http\Request\User;


use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => ['required','string','max:255'],
            'password' => ['required','string','min:6'],
        ];
    }
}
