<?php

namespace App\Http\Requests\Review;

use App\Traits\JsonValidationResponse;
use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
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
            'rating' => 'nullable|integer|min:1|max:10',
            'review_text' => 'nullable|string',
            'is_public' => 'boolean',
        ];
    }
}
