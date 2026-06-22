<?php

namespace App\Livewire\Order;

use Livewire\Component;
use App\Models\Order;
use App\Models\BusinessSetting;
use PDF;

class Invoice extends Component
{
    public $orderId;
    public $order;
    public $settings;

    public function mount($id)
    {
        $this->orderId = $id;
        $this->loadOrder();
        $this->settings = BusinessSetting::first();
    }

    public function loadOrder()
    {
        $this->order = Order::with(['customer', 'items', 'payments.method', 'creator'])
            ->find($this->orderId);

        if (!$this->order) {
            session()->flash('error', 'Order not found');
            return $this->redirectRoute('orders.index', navigate: true);
        }
    }

    public function downloadInvoice()
    {
        $order = $this->order;

        // Log activity
        logActivity('downloaded_invoice', $order, [], [
            'order_number' => $order->order_number,
            'customer_name' => $order->customer_name,
            'total_amount' => $order->total_amount
        ]);

        try {
            $pdf = PDF::loadView('livewire.orders.invoice-pdf', [
                'order' => $order,
                'settings' => $this->settings,
                'receipt_footer' => $this->settings->receipt_footer ?? 'Thank you for your business!'
            ]);
            $pdf->setPaper('A4', 'portrait');
            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->output();
            }, 'invoice-' . $order->order_number . '.pdf');
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => 'Error generating invoice: ' . $e->getMessage(),
                'type' => 'error'
            ]);
            return $this->fallbackHtmlDownload($order);
        }
    }

    public function printInvoice()
    {
        $this->dispatch('print-invoice');
    }

    public function goBack()
    {
        return $this->redirectRoute('orders.index', navigate: true);
    }

    public function render()
    {
        return view('livewire.orders.invoice', [
            'order' => $this->order,
            'settings' => $this->settings,
        ])->layout('layouts.app');
    }
}