<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_number',
        'supplier_id',
        'purchase_date',
        'expected_delivery_date',
        'delivery_date',
        'status',
        'payment_status',
        'subtotal',
        'tax_amount',
        'discount_type',
        'discount_value',
        'discount_amount',
        'shipping_cost',
        'other_cost',
        'total_amount',
        'paid_amount',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'expected_delivery_date' => 'date',
        'delivery_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'other_cost' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];

    // Relationships
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeOrdered($query)
    {
        return $query->where('status', 'ordered');
    }

    public function scopeReceived($query)
    {
        return $query->where('status', 'received');
    }

    public function scopePartial($query)
    {
        return $query->where('status', 'partial');
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        $colors = [
            'draft' => 'gray',
            'ordered' => 'blue',
            'partial' => 'orange',
            'received' => 'green',
            'cancelled' => 'red',
        ];
        
        return $colors[$this->status] ?? 'gray';
    }

    public function getDueAmountAttribute()
{
    return $this->total_amount - $this->paid_amount;
}
}