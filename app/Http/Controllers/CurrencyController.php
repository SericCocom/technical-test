<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCurrencyRequest;
use App\Http\Requests\UpdateCurrencyRequest;
use App\Models\Currency;
use Illuminate\Http\JsonResponse;

class CurrencyController extends Controller
{
    /**
     * Display a listing of the currencies.
     */
    public function index(): JsonResponse
    {
        $currencies = Currency::all();
        
        return response()->json([
            'success' => true,
            'data' => $currencies,
        ]);
    }

    /**
     * Store a newly created currency in storage.
     */
    public function store(StoreCurrencyRequest $request): JsonResponse
    {
        $currency = Currency::create($request->validated());
        
        return response()->json([
            'success' => true,
            'message' => 'Currency created successfully',
            'data' => $currency,
        ], 201);
    }

    /**
     * Display the specified currency.
     */
    public function show(Currency $currency): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $currency,
        ]);
    }

    /**
     * Update the specified currency in storage.
     */
    public function update(UpdateCurrencyRequest $request, Currency $currency): JsonResponse
    {
        $currency->update($request->validated());
        
        return response()->json([
            'success' => true,
            'message' => 'Currency updated successfully',
            'data' => $currency,
        ]);
    }

    /**
     * Remove the specified currency from storage.
     */
    public function destroy(Currency $currency): JsonResponse
    {
        $currency->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Currency deleted successfully',
        ]);
    }
}
