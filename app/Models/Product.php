<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'sku',
        'barcode',
        'description',
        'category_id',
        'brand_id',
        'unit_id',
        'purchase_price',
        'selling_price',
        'wholesale_price',
        'min_price',
        'max_price',
        'tax_rate',
        'tax_type',
        'discount_type',
        'discount_value',
        'stock_quantity',
        'low_stock_threshold',
        'allow_out_of_stock',
        'is_active',
        'is_featured',
        'main_image',
        'created_by',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'wholesale_price' => 'decimal:2',
        'min_price' => 'decimal:2',
        'max_price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'stock_quantity' => 'integer',
        'low_stock_threshold' => 'integer',
        'allow_out_of_stock' => 'boolean',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

      protected function createdAt(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (!$value) return null;
                return \Carbon\Carbon::parse($value)->format(dateFormat() . ' ' . timeFormat());
            },
        );
    }
    
    protected function updatedAt(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (!$value) return null;
                return \Carbon\Carbon::parse($value)->format(dateFormat() . ' ' . timeFormat());
            },
        );
    }

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }

   

    public function stockAdjustments()
    {
        return $this->hasMany(StockAdjustment::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    public function scopeLowStock($query)
    {
        return $query->whereRaw('stock_quantity <= low_stock_threshold');
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('stock_quantity', '<=', 0);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    // Accessors
    public function getIsLowStockAttribute()
    {
        return $this->stock_quantity <= $this->low_stock_threshold;
    }

    public function getIsOutOfStockAttribute()
    {
        return $this->stock_quantity <= 0;
    }

    public function getFinalPriceAttribute()
    {
        $price = $this->selling_price;
        
        if ($this->discount_type && $this->discount_value) {
            if ($this->discount_type === 'percentage') {
                $price = $price - ($price * $this->discount_value / 100);
            } else {
                $price = $price - $this->discount_value;
            }
        }
        
        return max($price, 0);
    }

    public function getPriceWithTaxAttribute()
    {
        $price = $this->final_price;
        
        if ($this->tax_rate > 0) {
            if ($this->tax_type === 'inclusive') {
                return $price;
            }
            return $price + ($price * $this->tax_rate / 100);
        }
        
        return $price;
    }
}