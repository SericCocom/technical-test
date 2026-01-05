<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'currency_id' => ['sometimes', 'exists:currencies,id'],
            'tax_cost' => ['sometimes', 'numeric', 'min:0'],
            'manufacturing_cost' => ['sometimes', 'numeric', 'min:0'],
            'additional_prices' => ['nullable', 'array'],
            'additional_prices.*.currency_id' => ['required', 'exists:currencies,id'],
            'additional_prices.*.price' => ['required', 'numeric', 'min:0'],
        ];
    }
}
