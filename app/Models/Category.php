<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'parent_id',
        'image',
        'is_active',
        'sort_order',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'parent_id' => 'integer', // Cast to integer
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


    // Boot method to handle empty strings
    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($category) {
            // Convert empty parent_id to null
            if (isset($category->parent_id) && $category->parent_id === '') {
                $category->parent_id = null;
            }
        });
    }

    // Relationships
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeParentCategories($query)
    {
        return $query->whereNull('parent_id');
    }

    // Accessors
    public function getFullNameAttribute()
    {
        if ($this->parent) {
            return $this->parent->name . ' > ' . $this->name;
        }
        return $this->name;
    }
}