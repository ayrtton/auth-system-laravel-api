<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize() {
        return true;
    }

    public function rules() {
        return [
            'name' => 'required',
            'email' => 'required|email|unique|min:10|max:60',
            'password' => [
                'required', 'string', 'confirmed',
                Password::min(8)//->letters()->numbers()->mixedCase()->uncompromised()
            ],
        ];
    }
}
