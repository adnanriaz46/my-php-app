<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ApiKeyAuth
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $apiKey = $request->header('X-API-Key') ?? $request->query('api_key');
        $validApiKey = env('WHOLESALE_API_KEY');

        if (!$validApiKey || !hash_equals((string) $validApiKey, (string) $apiKey)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or missing API key',
            ], 401);
        }

        return $next($request);
    }
}
