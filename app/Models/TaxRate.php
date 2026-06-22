<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class TaxRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'rate',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'rate' => 'decimal:2',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
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

    public function orders()
    {
        return $this->hasMany(Order::class, 'tax_rate_id');
    }

    public static function getDefault()
    {
        return self::where('is_default', true)->first() ?? self::first();
    }
}
