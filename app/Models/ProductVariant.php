<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'name',
        'value',
        'sku',
        'barcode',
        'additional_price',
        'stock_quantity',
        'is_active',
    ];

    protected $casts = [
        'additional_price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'variant_id');
    }

    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class, 'variant_id');
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'variant_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Accessors
    public function getFullPriceAttribute()
    {
        return $this->product->selling_price + $this->additional_price;
    }

    public function getFullNameAttribute()
    {
        return $this->product->name . ' - ' . $this->name . ': ' . $this->value;
    }
}