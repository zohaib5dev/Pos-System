<?php

namespace App\Http\Controllers;

use App\Models\BusinessSetting;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Http\Request;

class ReceiptController extends Controller
{
    public function show($orderId)
    {
        $order = Order::with(['items', 'customer', 'payments.method', 'creator'])
            ->findOrFail($orderId);
        
        // Get business settings
        //$settings = BusinessSetting::pluck('value', 'key')->toArray();
        
        return view('livewire.pos-receipt', [
            'order' => $order,
            'business_name' =>  'Your Store',
            'business_address' =>  '123 Main St, City',
            'business_phone' =>  '(123) 456-7890',
            'business_email' =>  'info@store.com',
            'tax_rate' =>  0,
            'receipt_footer' =>  'Thank you for your business!',
        ]);
    }
}