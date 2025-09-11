<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WholesaleProperty;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class WholesalePropertyApiController extends Controller
{
    /**
     * Get wholesale properties with pagination and filters
     */
    public function index(Request $request): JsonResponse
    {
        $query = WholesaleProperty::query()
            ->where('approved', true)
            ->where('status', 'active');

        // Apply filters
        if ($request->has('county') && $request->county) {
            $query->where('county', $request->county);
        }

        if ($request->has('city') && $request->city) {
            $query->where('city_name', 'like', '%' . $request->city . '%');
        }

        if ($request->has('state') && $request->state) {
            $query->where('state_or_province', $request->state);
        }

        if ($request->has('zip') && $request->zip) {
            $query->where('zip_code', $request->zip);
        }

        if ($request->has('min_price') && $request->min_price) {
            $query->where('list_price', '>=', $request->min_price);
        }

        if ($request->has('max_price') && $request->max_price) {
            $query->where('list_price', '<=', $request->max_price);
        }

        if ($request->has('beds') && $request->beds) {
            $query->where('beds', '>=', $request->beds);
        }

        if ($request->has('baths') && $request->baths) {
            $query->where('baths', '>=', $request->baths);
        }

        if ($request->has('structure_type') && $request->structure_type) {
            $query->where('structure_type', $request->structure_type);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = min($request->get('per_page', 20), 100); // Max 100 per page
        $properties = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $properties->items(),
            'pagination' => [
                'current_page' => $properties->currentPage(),
                'last_page' => $properties->lastPage(),
                'per_page' => $properties->perPage(),
                'total' => $properties->total(),
                'from' => $properties->firstItem(),
                'to' => $properties->lastItem(),
            ],
        ]);
    }

    /**
     * Get a specific wholesale property by ID
     */
    public function show(Request $request, $id): JsonResponse
    {
        $property = WholesaleProperty::where('id', $id)
            ->where('approved', true)
            ->where('status', 'active')
            ->first();

        if (!$property) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found or not available',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $property,
        ]);
    }

    /**
     * Get wholesale property by slug
     */
    public function showBySlug(Request $request, $slug): JsonResponse
    {
        $property = WholesaleProperty::where('slug', $slug)
            ->where('approved', true)
            ->where('status', 'active')
            ->first();

        if (!$property) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found or not available',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $property,
        ]);
    }

    /**
     * Search wholesale properties by address
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'required|string|min:3',
        ]);

        $query = $request->get('query');
        
        $properties = WholesaleProperty::where('approved', true)
            ->where('status', 'active')
            ->where(function ($q) use ($query) {
                $q->where('full_street_address', 'like', '%' . $query . '%')
                  ->orWhere('city_name', 'like', '%' . $query . '%')
                  ->orWhere('county', 'like', '%' . $query . '%')
                  ->orWhere('zip_code', 'like', '%' . $query . '%');
            })
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $properties,
            'count' => $properties->count(),
        ]);
    }

    /**
     * Get available counties for filtering
     */
    public function counties(): JsonResponse
    {
        $counties = WholesaleProperty::where('approved', true)
            ->where('status', 'active')
            ->whereNotNull('county')
            ->distinct()
            ->pluck('county')
            ->filter()
            ->sort()
            ->values();

        return response()->json([
            'success' => true,
            'data' => $counties,
        ]);
    }

    /**
     * Get available cities for a specific county
     */
    public function cities(Request $request): JsonResponse
    {
        $request->validate([
            'county' => 'required|string',
        ]);

        $cities = WholesaleProperty::where('approved', true)
            ->where('status', 'active')
            ->where('county', $request->county)
            ->whereNotNull('city_name')
            ->distinct()
            ->pluck('city_name')
            ->filter()
            ->sort()
            ->values();

        return response()->json([
            'success' => true,
            'data' => $cities,
        ]);
    }

    /**
     * Get property statistics
     */
    public function stats(): JsonResponse
    {
        $stats = [
            'total_properties' => WholesaleProperty::where('approved', true)
                ->where('status', 'active')
                ->count(),
            'avg_price' => WholesaleProperty::where('approved', true)
                ->where('status', 'active')
                ->whereNotNull('list_price')
                ->avg('list_price'),
            'min_price' => WholesaleProperty::where('approved', true)
                ->where('status', 'active')
                ->whereNotNull('list_price')
                ->min('list_price'),
            'max_price' => WholesaleProperty::where('approved', true)
                ->where('status', 'active')
                ->whereNotNull('list_price')
                ->max('list_price'),
            'counties_count' => WholesaleProperty::where('approved', true)
                ->where('status', 'active')
                ->whereNotNull('county')
                ->distinct('county')
                ->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}
