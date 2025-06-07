<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class RegisterUser extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|min:4|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|alpha_dash',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Имя пользователя - обязательное поле',
            'name.string' => 'Имя пользователя должно быть в виде строки',
            'name.min' => 'Минимальная длина имени пользователя 4 символа',
            'name.max' => 'Максимальная длина имени пользователя 255 символов',

            'email.required' => 'Email - обязательное поле',
            'email.string' => 'Email должен быть в виде строки',
            'email.unique' => 'Пользователь с таким email уже существует',

            'password.required' => 'Пароль - обязательное поле',
            'password.string' => 'Пароль должен быть в виде строки',
            'password.min' => 'Пароль должен быть минимум 6 символов',
            'password.alpha_dash' => 'Пароль не может содержать ничего кроме букв, цифр, дефиса и нижнего подчеркивания',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json(
            [
                'message' => $validator->errors(),
            ],
            Response::HTTP_BAD_REQUEST)
        );
    }
}
