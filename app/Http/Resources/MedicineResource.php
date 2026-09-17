<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MedicineResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'category' => $this->category,
            'unit' => $this->unit,
            'purchase_price' => $this->purchase_price,
            'selling_price' => $this->selling_price,
            'stock' => $this->stock,
            'minimum_stock' => $this->minimum_stock,
            'expired_date' => $this->expired_date,
            'notes' => $this->notes,
            'status_label' => $this->status_label,
        ];
    }
}
