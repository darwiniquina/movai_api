<?php

namespace App\Http\Requests\User;

use App\Traits\JsonValidationResponse;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    use JsonValidationResponse;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'nullable|string',
            'display_name' => 'nullable|string',
            'bio' => 'nullable|string',
            'public_profile' => 'boolean',
            'emoji_avatar' => 'nullable|string',
        ];
    }
}
