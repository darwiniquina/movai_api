<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class LogoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Only allow authenticated users to call logout
        return Auth::check();
    }

    public function rules(): array
    {
        return [];
    }
}
