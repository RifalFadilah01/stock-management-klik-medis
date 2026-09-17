<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = [
        'status_label'
    ];

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        $has = fn (string $key) => isset($filters[$key]) && $filters[$key] !== '';

        if ($has('search')) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            });
        }

        if ($has('category')) {
            $query->where('category', $filters['category']);
        }

        if ($has('status')) {
            $status = $filters['status'];
            if ($status === 'Stok Habis') {
                $query->where('stock', 0);
            } elseif ($status === 'Stok Menipis') {
                $query->where('stock', '>', 0)->whereColumn('stock', '<=', 'minimum_stock');
            } elseif ($status === 'Kedaluwarsa') {
                $query->where('expired_date', '<', now()->toDateString());
            } elseif ($status === 'Tersedia') {
                $query->whereColumn('stock', '>', 'minimum_stock')
                    ->where('expired_date', '>=', now()->toDateString());
            }
        }

        return $query;
    }

    protected function statusLabel(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->stock === 0) {
                    return 'Stok Habis';
                }
                
                if ($this->stock <= $this->minimum_stock && $this->stock > 0) {
                    return 'Stok Menipis';
                }
                
                if (Carbon::parse($this->expired_date)->isPast()) {
                    return 'Kedaluwarsa';
                }
                
                return 'Tersedia';
            }
        );
    }
}