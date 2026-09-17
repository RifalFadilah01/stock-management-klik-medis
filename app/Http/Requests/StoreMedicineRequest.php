<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMedicineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'unique:medicines,code'],
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:255'],
            'unit' => ['required', 'string', 'max:255'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
            'expired_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
