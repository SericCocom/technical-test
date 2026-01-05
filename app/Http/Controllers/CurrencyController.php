<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCurrencyRequest;
use App\Http\Requests\UpdateCurrencyRequest;
use App\Models\Currency;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class CurrencyController extends Controller
{
    #[OA\Get(
        path: '/api/currencies',
        summary: 'Get all currencies',
        tags: ['Currencies'],
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
        $currencies = Currency::all();
        
        return response()->json([
            'success' => true,
            'data' => $currencies,
        ]);
    }

    #[OA\Post(
        path: '/api/currencies',
        summary: 'Create a new currency',
        tags: ['Currencies'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['code', 'name', 'symbol'],
                properties: [
                    new OA\Property(property: 'code', type: 'string', example: 'USD'),
                    new OA\Property(property: 'name', type: 'string', example: 'US Dollar'),
                    new OA\Property(property: 'symbol', type: 'string', example: '$')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Currency created successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Currency created successfully'),
                        new OA\Property(property: 'data', type: 'object')
                    ]
                )
            )
        ]
    )]
    public function store(StoreCurrencyRequest $request): JsonResponse
    {
        $currency = Currency::create($request->validated());
        
        return response()->json([
            'success' => true,
            'message' => 'Currency created successfully',
            'data' => $currency,
        ], 201);
    }

    #[OA\Get(
        path: '/api/currencies/{id}',
        summary: 'Get a specific currency',
        tags: ['Currencies'],
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
    public function show(Currency $currency): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $currency,
        ]);
    }

    #[OA\Put(
        path: '/api/currencies/{id}',
        summary: 'Update a currency',
        tags: ['Currencies'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'code', type: 'string', example: 'EUR'),
                    new OA\Property(property: 'name', type: 'string', example: 'Euro'),
                    new OA\Property(property: 'symbol', type: 'string', example: '€')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Currency updated successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Currency updated successfully'),
                        new OA\Property(property: 'data', type: 'object')
                    ]
                )
            )
        ]
    )]
    public function update(UpdateCurrencyRequest $request, Currency $currency): JsonResponse
    {
        $currency->update($request->validated());
        
        return response()->json([
            'success' => true,
            'message' => 'Currency updated successfully',
            'data' => $currency,
        ]);
    }

    #[OA\Delete(
        path: '/api/currencies/{id}',
        summary: 'Delete a currency',
        tags: ['Currencies'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Currency deleted successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Currency deleted successfully')
                    ]
                )
            )
        ]
    )]
    public function destroy(Currency $currency): JsonResponse
    {
        $currency->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Currency deleted successfully',
        ]);
    }
}
