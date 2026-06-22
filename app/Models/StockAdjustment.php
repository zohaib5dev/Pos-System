<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class StockAdjustment extends Model
{
    use HasFactory;

    protected $fillable = [
        'adjustment_number',
        'product_id',
        'variant_id',
        'adjustment_type',
        'quantity',
        'current_quantity',
        'new_quantity',
        'reason',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'current_quantity' => 'integer',
        'new_quantity' => 'integer',
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
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

  

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}