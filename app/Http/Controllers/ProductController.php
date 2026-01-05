<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     */
    public function index(): JsonResponse
    {
        $products = Product::with(['currency', 'prices.currency'])->get();
        
        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        $validated = $request->validated();
        
        // Extract additional prices if present
        $additionalPrices = $validated['additional_prices'] ?? [];
        unset($validated['additional_prices']);
        
        // Create the product
        $product = Product::create($validated);
        
        // Create additional prices in other currencies
        if (!empty($additionalPrices)) {
            foreach ($additionalPrices as $priceData) {
                $product->prices()->create($priceData);
            }
        }
        
        // Load relationships
        $product->load(['currency', 'prices.currency']);
        
        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'data' => $product,
        ], 201);
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product): JsonResponse
    {
        $product->load(['currency', 'prices.currency']);
        
        return response()->json([
            'success' => true,
            'data' => $product,
        ]);
    }

    /**
     * Update the specified product in storage.
     */
    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $validated = $request->validated();
        
        // Extract additional prices if present
        $additionalPrices = $validated['additional_prices'] ?? null;
        unset($validated['additional_prices']);
        
        // Update the product
        $product->update($validated);
        
        // Update additional prices if provided
        if ($additionalPrices !== null) {
            // Delete existing prices and create new ones
            $product->prices()->delete();
            
            foreach ($additionalPrices as $priceData) {
                $product->prices()->create($priceData);
            }
        }
        
        // Load relationships
        $product->load(['currency', 'prices.currency']);
        
        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'data' => $product,
        ]);
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product): JsonResponse
    {
        $product->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully',
        ]);
    }
}
