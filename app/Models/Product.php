<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'sku',
        'category_id',
        'brand',
        'price',
        'compare_price',
        'stock_qty',
        'status',
        'featured',
        'short_desc',
        'long_desc',
        'video_path',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'compare_price' => 'decimal:2',
            'stock_qty' => 'integer',
            'featured' => 'boolean',
        ];
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('position');
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews()
    {
        return $this->hasMany(Review::class)->where('status', 'approved');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** Percentage saved vs. compare_price, or null when not on sale. */
    public function getDiscountPercentAttribute(): ?int
    {
        if ($this->compare_price && $this->compare_price > $this->price) {
            return (int) round((1 - $this->price / $this->compare_price) * 100);
        }
        return null;
    }
}
