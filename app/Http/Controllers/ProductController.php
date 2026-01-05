<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'My API',
    attachables: [new OA\Attachable()]
)]
class ProductController extends Controller
{
    #[OA\Get(
        path: '/api/products',
        summary: 'Get all products',
        tags: ['Products'],
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
    public function index(): JsonResponse
    {
        $products = Product::with(['currency', 'prices.currency'])->get();
        
        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    #[OA\Post(
        path: '/api/products',
        summary: 'Create a new product',
        tags: ['Products'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'description', 'price', 'currency_id'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Product Name'),
                    new OA\Property(property: 'description', type: 'string', example: 'Product description'),
                    new OA\Property(property: 'price', type: 'number', format: 'float', example: 99.99),
                    new OA\Property(property: 'currency_id', type: 'integer', example: 1),
                    new OA\Property(property: 'additional_prices', type: 'array', items: new OA\Items(type: 'object'))
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Product created successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Product created successfully'),
                        new OA\Property(property: 'data', type: 'object')
                    ]
                )
            )
        ]
    )]
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

    #[OA\Get(
        path: '/api/products/{id}',
        summary: 'Get a specific product',
        tags: ['Products'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful operation',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'data', type: 'object')
                    ]
                )
            )
        ]
    )]
    public function show(Product $product): JsonResponse
    {
        $product->load(['currency', 'prices.currency']);
        
        return response()->json([
            'success' => true,
            'data' => $product,
        ]);
    }

    #[OA\Put(
        path: '/api/products/{id}',
        summary: 'Update a product',
        tags: ['Products'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string'),
                    new OA\Property(property: 'description', type: 'string'),
                    new OA\Property(property: 'price', type: 'number', format: 'float'),
                    new OA\Property(property: 'currency_id', type: 'integer'),
                    new OA\Property(property: 'additional_prices', type: 'array', items: new OA\Items(type: 'object'))
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Product updated successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Product updated successfully'),
                        new OA\Property(property: 'data', type: 'object')
                    ]
                )
            )
        ]
    )]
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

    #[OA\Delete(
        path: '/api/products/{id}',
        summary: 'Delete a product',
        tags: ['Products'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Product deleted successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Product deleted successfully')
                    ]
                )
            )
        ]
    )]
    public function destroy(Product $product): JsonResponse
    {
        $product->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully',
        ]);
    }
}
