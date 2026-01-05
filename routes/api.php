<?php

use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductPriceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Currency routes
Route::apiResource('currencies', CurrencyController::class);

// Product routes
Route::apiResource('products', ProductController::class);

// Product prices routes (nested resource)
Route::apiResource('products.prices', ProductPriceController::class)
    ->only(['index', 'store']);
