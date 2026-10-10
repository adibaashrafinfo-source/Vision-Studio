<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'name',
        'value',
        'price_delta',
        'stock_qty',
    ];

    protected function casts(): array
    {
        return [
            'price_delta' => 'decimal:2',
            'stock_qty' => 'integer',
        ];
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
