<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductPriceRequest;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class ProductPriceController extends Controller
{
    /**
     * Display a listing of prices for a specific product.
     */
    public function index(Product $product): JsonResponse
    {
        $prices = $product->prices()->with('currency')->get();
        
        return response()->json([
            'success' => true,
            'data' => $prices,
        ]);
    }

    /**
     * Store a newly created price for a specific product.
     */
    public function store(StoreProductPriceRequest $request, Product $product): JsonResponse
    {
        $validated = $request->validated();
        
        // Check if price already exists for this currency
        $existingPrice = $product->prices()
            ->where('currency_id', $validated['currency_id'])
            ->first();
            
        if ($existingPrice) {
            return response()->json([
                'success' => false,
                'message' => 'Price for this currency already exists. Use PUT to update it.',
            ], 422);
        }
        
        $price = $product->prices()->create($validated);
        $price->load('currency');
        
        return response()->json([
            'success' => true,
            'message' => 'Price created successfully',
            'data' => $price,
        ], 201);
    }
}
