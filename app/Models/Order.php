<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'invoice_number',
        'customer_id',
        'tax_rate_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'order_date',
        'due_date',
        'order_type',
        'status',
        'payment_status',
        'shipping_status',
        'subtotal',
        'tax_amount',
        'discount_type',
        'discount_value',
        'discount_amount',
        'shipping_cost',
        'other_charges',
        'total_amount',
        'paid_amount',
        'due_amount',
        'change_amount',
        'notes',
        'staff_notes',
        'created_by',
    ];

    protected $casts = [
        'order_date' => 'datetime',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'other_charges' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_amount' => 'decimal:2',
        'change_amount' => 'decimal:2',
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
    public function taxRate()
    {
        return $this->belongsTo(TaxRate::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function loyaltyPoints()
    {
        return $this->hasMany(LoyaltyPoint::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopeUnpaid($query)
    {
        return $query->whereIn('payment_status', ['pending', 'partial']);
    }

    public function scopeOverdue($query)
    {
        return $query->where('payment_status', 'overdue')
            ->where('due_date', '<', now());
    }

    public function scopeDateRange($query, $start, $end)
    {
        return $query->whereBetween('order_date', [$start, $end]);
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        $colors = [
            'pending' => 'yellow',
            'processing' => 'blue',
            'completed' => 'green',
            'cancelled' => 'red',
            'refunded' => 'purple',
        ];

        return $colors[$this->status] ?? 'gray';
    }

    public function getPaymentStatusBadgeAttribute()
    {
        $colors = [
            'pending' => 'yellow',
            'partial' => 'orange',
            'paid' => 'green',
            'overdue' => 'red',
        ];

        return $colors[$this->payment_status] ?? 'gray';
    }
}
