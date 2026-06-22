<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'profile_photo_path',
        'is_active',
        'last_login_at',
        'last_login_ip',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    // Relationships
    public function createdProducts()
    {
        return $this->hasMany(Product::class, 'created_by');
    }

    public function createdOrders()
    {
        return $this->hasMany(Order::class, 'created_by');
    }

    public function createdPurchases()
    {
        return $this->hasMany(Purchase::class, 'created_by');
    }

    public function createdExpenses()
    {
        return $this->hasMany(Expense::class, 'created_by');
    }

    public function createdPayments()
    {
        return $this->hasMany(Payment::class, 'created_by');
    }

    public function createdCustomers()
    {
        return $this->hasMany(Customer::class, 'created_by');
    }

    public function createdSuppliers()
    {
        return $this->hasMany(Supplier::class, 'created_by');
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function customer()
    {
        return $this->hasOne(Customer::class);
    }
}