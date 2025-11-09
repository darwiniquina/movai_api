<?php

namespace App\Http\Requests\WatchList;

use App\Traits\JsonValidationResponse;
use Illuminate\Foundation\Http\FormRequest;

class StoreWatchlistReqeuest extends FormRequest
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
            'status' => 'in:planning,completed',
            'notes' => 'nullable|string',
        ];
    }
}
