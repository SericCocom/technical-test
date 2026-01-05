<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductPriceRequest;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class ProductPriceController extends Controller
{
    #[OA\Get(
        path: '/api/products/{product_id}/prices',
        summary: 'Get all prices for a specific product',
        tags: ['Product Prices'],
        parameters: [
            new OA\Parameter(name: 'product_id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful operation',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(type: 'object'))
                    ]
                )
            )
        ]
    )]
    public function index(Product $product): JsonResponse
    {
        $prices = $product->prices()->with('currency')->get();
        
        return response()->json([
            'success' => true,
            'data' => $prices,
        ]);
    }

    #[OA\Post(
        path: '/api/products/{product_id}/prices',
        summary: 'Create a new price for a specific product',
        tags: ['Product Prices'],
        parameters: [
            new OA\Parameter(name: 'product_id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['currency_id', 'price'],
                properties: [
                    new OA\Property(property: 'currency_id', type: 'integer', example: 2),
                    new OA\Property(property: 'price', type: 'number', format: 'float', example: 85.50)
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Price created successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Price created successfully'),
                        new OA\Property(property: 'data', type: 'object')
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Price already exists for this currency',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Price for this currency already exists. Use PUT to update it.')
                    ]
                )
            )
        ]
    )]
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
