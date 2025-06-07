<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'password' => 'required|string|min:6|alpha_dash',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Email - обязательное поле',
            'email.string' => 'Email должен быть в виде строки',

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
