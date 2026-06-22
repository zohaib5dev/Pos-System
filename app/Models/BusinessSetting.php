<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_name',
        'business_logo',
        'business_address',
        'business_phone',
        'business_email',
        'tax_number',
        'registration_number',
        'currency_code',
        'currency_symbol',
        'timezone',
        'date_format',
        'time_format',
        'receipt_footer',
    ];

    protected $table = 'business_settings';

    // Singleton pattern - only one record
    public static function getSettings()
    {
        $settings = self::first();
        
        if (!$settings) {
            $settings = self::create([
                'business_name' => 'My Business',
                'currency_code' => 'USD',
                'currency_symbol' => '$',
            ]);
        }
        
        return $settings;
    }



    public function formatCurrency($amount)
    {
        return $this->currency_symbol . number_format($amount, 2);
    }

    public function formatDate($date)
    {
        return $date->format($this->date_format ?? 'Y-m-d');
    }


}