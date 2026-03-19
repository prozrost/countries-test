<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CountryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CountryController extends Controller
{
    public function __construct(
        private readonly CountryService $countries,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $page = max(1, (int) $request->input('page', 1));
        $perPage = min(100, max(1, (int) $request->input('per_page', 20)));

        try {
            $payload = $this->countries->page($page, $perPage);
        } catch (\Throwable $e) {
            Log::error('Countries endpoint error', ['message' => $e->getMessage()]);

            return response()->json(
                ['message' => 'Unable to load countries. Try again later.'],
                503
            );
        }

        if ($payload === null) {
            return response()->json(
                ['message' => 'Unable to load countries. Try again later.'],
                503
            );
        }

        return response()->json($payload);
    }
}
