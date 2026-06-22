<?php

namespace App\Livewire\Purchase;

use Livewire\Component;
use App\Models\Purchase;
use App\Models\Product;
use App\Models\PurchaseItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Receive extends Component
{
    public $purchaseId;
    public $purchase;
    public $receiveItems = [];

    public function mount($id)
    {
        $this->purchaseId = $id;
        $this->loadPurchase();
        $this->loadReceiveItems();
    }

    public function loadPurchase()
    {
        $this->purchase = Purchase::with(['supplier'])->find($this->purchaseId);

        if (!$this->purchase) {
            session()->flash('error', 'Purchase not found');
            return $this->redirectRoute('purchases.index', navigate: true);
        }
    }

    public function loadReceiveItems()
    {
        $this->receiveItems = PurchaseItem::where('purchase_id', $this->purchaseId)
            ->with('product')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name ?? $item->product_name,
                    'sku' => $item->product->sku ?? $item->sku,
                    'ordered_quantity' => $item->quantity,
                    'received_quantity' => $item->received_quantity,
                    'remaining_quantity' => $item->quantity - $item->received_quantity,
                    'receive_quantity' => 0,
                    'unit_cost' => $item->unit_cost,
                ];
            })
            ->toArray();
    }

    public function processReceive()
    {
        $this->validate([
            'receiveItems.*.receive_quantity' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();

        try {
            $allReceived = true;
            $receivedItems = [];

            foreach ($this->receiveItems as $receiveItem) {
                if ($receiveItem['receive_quantity'] > 0) {
                    $purchaseItem = PurchaseItem::find($receiveItem['id']);
                    $newReceived = $purchaseItem->received_quantity + $receiveItem['receive_quantity'];

                    if ($newReceived > $purchaseItem->quantity) {
                        DB::rollBack();
                        $this->dispatch('notify', [
                            'message' => 'Receive quantity cannot exceed ordered quantity for ' . $receiveItem['product_name'],
                            'type' => 'error'
                        ]);
                        return;
                    }

                    $purchaseItem->received_quantity = $newReceived;
                    $purchaseItem->save();

                    // Update product stock
                    $product = Product::find($receiveItem['product_id']);
                    if ($product) {
                        $product->stock_quantity += $receiveItem['receive_quantity'];
                        $product->save();
                    }

                    $receivedItems[] = [
                        'product_id' => $receiveItem['product_id'],
                        'product_name' => $receiveItem['product_name'],
                        'quantity' => $receiveItem['receive_quantity'],
                    ];

                    if ($newReceived < $purchaseItem->quantity) {
                        $allReceived = false;
                    }
                }
            }

            if (empty($receivedItems)) {
                $this->dispatch('notify', [
                    'message' => 'No items to receive',
                    'type' => 'error'
                ]);
                return;
            }

            // Update purchase status
            $purchase = Purchase::find($this->purchaseId);
            $oldStatus = $purchase->status;
            
            if ($allReceived) {
                $purchase->status = 'received';
                $purchase->delivery_date = now();
            } else {
                $purchase->status = 'partial';
            }

            $purchase->save();

            DB::commit();

            // Log activity
            logActivity(
                'items_received',
                $purchase,
                ['status' => $oldStatus],
                [
                    'status' => $purchase->status,
                    'received_items' => $receivedItems,
                    'delivery_date' => $purchase->delivery_date
                ]
            );

            $this->dispatch('notify', [
                'message' => 'Items received successfully',
                'type' => 'success'
            ]);

            return $this->redirectRoute('purchases.show', ['id' => $this->purchaseId], navigate: true);

        } catch (\Exception $e) {
            DB::rollBack();
            
            $this->dispatch('notify', [
                'message' => 'Error receiving items: ' . $e->getMessage(),
                'type' => 'error'
            ]);
            
            Log::error('Receive error: ' . $e->getMessage());
        }
    }

    public function goBack()
    {
        return $this->redirectRoute('purchases.index', navigate: true);
    }

    public function render()
    {
        return view('livewire.purchases.receive', [
            'purchase' => $this->purchase,
            'receiveItems' => $this->receiveItems,
        ])->layout('layouts.app');
    }
}