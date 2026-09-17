<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMedicineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $medicineId = $this->route('medicine')->id ?? $this->route('medicine');

        return [
            'code' => ['sometimes', 'required', 'string', 'unique:medicines,code,' . $medicineId],
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'category' => ['sometimes', 'required', 'string', 'max:255'],
            'unit' => ['sometimes', 'required', 'string', 'max:255'],
            'purchase_price' => ['sometimes', 'required', 'numeric', 'min:0'],
            'selling_price' => ['sometimes', 'required', 'numeric', 'min:0'],
            'stock' => ['sometimes', 'required', 'integer', 'min:0'],
            'minimum_stock' => ['sometimes', 'required', 'integer', 'min:0'],
            'expired_date' => ['sometimes', 'required', 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
