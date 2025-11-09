<?php

namespace App\Http\Requests\Favorite;

use App\Traits\JsonValidationResponse;
use Illuminate\Foundation\Http\FormRequest;

class StoreFavoriteRequest extends FormRequest
{
    use JsonValidationResponse;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tmdb_id' => 'required|integer',
            'type' => 'required|in:movie,tv',
        ];
    }
}
