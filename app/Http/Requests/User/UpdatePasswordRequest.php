<?php

namespace App\Http\Requests\User;

use App\Traits\JsonValidationResponse;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePasswordRequest extends FormRequest
{
    use JsonValidationResponse;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'password' => 'required|string|min:6',
            'password_confirmation' => 'required|same:password',
            'current_password' => 'required|string',
        ];
    }
}
