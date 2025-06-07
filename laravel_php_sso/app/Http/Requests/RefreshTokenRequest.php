<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class RefreshTokenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'refresh_token' => 'required|string|size:36',
        ];
    }

    public function messages(): array
    {
        return [
            'refresh_token.required' => 'refresh_token - обязательное поле',
            'refresh_token.string' => 'refresh_token должен быть в виде строки',
            'refresh_token.size' => 'refresh_token должен быть длиной 36 символов',
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
