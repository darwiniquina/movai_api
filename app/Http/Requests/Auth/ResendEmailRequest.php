<?php

namespace App\Http\Requests\Auth;

use App\Traits\JsonValidationResponse;
use Illuminate\Foundation\Http\FormRequest;

class ResendEmailRequest extends FormRequest
{
    use JsonValidationResponse;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email|exists:users,email',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Email is required.',
            'email.email' => 'Email must be a valid email address.',
            'email.exists' => 'Email should be existing.',
        ];
    }
}
