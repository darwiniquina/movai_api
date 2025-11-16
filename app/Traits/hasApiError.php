<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;

trait hasApiError
{
    public function apiError(string $message)
    {
        Log::error($message);

        return response()->json([
            'success' => false,
            'message' => 'Failed to fetch data',
            'error' => $message,
        ], 500);
    }
}
