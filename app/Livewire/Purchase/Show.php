<?php

namespace App\Livewire\Purchase;

use Livewire\Component;
use App\Models\Purchase;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\PaymentHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class Show extends Component
{
    public $purchaseId;
    public $purchase;

    // Payment Modal Properties
    public $showPaymentModal = false;
    public $paymentPurchaseId = null;
    public $paymentAmount;
    public $paymentMethod = 'cash';
    public $paymentReference;
    public $paymentNotes;

    // Status Update
    public $status;

    protected $rules = [
        'paymentAmount' => 'required|numeric|min:0.01',
        'paymentMethod' => 'required|string',
        'paymentReference' => 'nullable|string|max:255',
        'paymentNotes' => 'nullable|string|max:500',
    ];

    protected $listeners = [
        'refreshComponent' => '$refresh',
        'refreshPurchase' => 'loadPurchase',
    ];

    public function mount($id)
    {
        $this->purchaseId = $id;
        $this->loadPurchase();
    }

    public function loadPurchase()
    {
        $this->purchase = Purchase::with([
            'supplier', 
            'items.product', 
            'payments.method', 
            'creator'
        ])->find($this->purchaseId);

        if (!$this->purchase) {
            session()->flash('error', 'Purchase not found');
            return $this->redirectRoute('purchases.index', navigate: true);
        }

        $this->status = $this->purchase->status;
    }

    public function goBack()
    {
        return $this->redirectRoute('purchases.index', navigate: true);
    }

    public function updateStatus($purchaseId, $status)
    {
        $purchase = Purchase::find($purchaseId);
        if ($purchase) {
            $purchase->update(['status' => $status]);
            $this->purchase->refresh();
            $this->dispatch('notify', [
                'message' => 'Status updated successfully!',
                'type' => 'success'
            ]);
        }
    }

    public function openPaymentModal($id)
    {
        $this->paymentPurchaseId = $id;
        $purchase = Purchase::find($id);
        
        if (!$purchase) {
            $this->dispatch('notify', [
                'message' => 'Purchase not found',
                'type' => 'error'
            ]);
            return;
        }
        
        // Calculate due amount
        $dueAmount = $purchase->total_amount - $purchase->paid_amount;
        
        if ($dueAmount <= 0) {
            $this->dispatch('notify', [
                'message' => 'This purchase has no outstanding balance',
                'type' => 'error'
            ]);
            return;
        }
        
        $this->paymentAmount = $dueAmount;
        $this->showPaymentModal = true;
    }

    public function closePaymentModal()
    {
        $this->showPaymentModal = false;
        $this->reset(['paymentAmount', 'paymentMethod', 'paymentReference', 'paymentNotes']);
        $this->dispatch('modalClosed');
    }

    public function processPayment()
    {
        $this->validate([
            'paymentAmount' => 'required|numeric|min:0.01',
            'paymentMethod' => 'required',
        ]);

        DB::beginTransaction();

        try {
            $purchase = Purchase::find($this->paymentPurchaseId);
            
            if (!$purchase) {
                $this->dispatch('notify', [
                    'message' => 'Purchase not found',
                    'type' => 'error'
                ]);
                return;
            }
            
            $dueAmount = $purchase->total_amount - $purchase->paid_amount;
            $oldPaidAmount = $purchase->paid_amount;
            $oldPaymentStatus = $purchase->payment_status;

            if ($this->paymentAmount > $dueAmount) {
                $this->dispatch('notify', [
                    'message' => 'Payment amount cannot exceed due amount',
                    'type' => 'error'
                ]);
                return;
            }

            // Get or create payment method
            $paymentMethod = PaymentMethod::firstOrCreate(
                ['slug' => $this->paymentMethod],
                ['name' => ucfirst($this->paymentMethod), 'is_active' => true]
            );

            // Create payment
            $payment = Payment::create([
                'payment_number' => 'PAY-' . time() . '-' . rand(1000, 9999),
                'purchase_id' => $purchase->id,
                'supplier_id' => $purchase->supplier_id,
                'payment_method_id' => $paymentMethod->id,
                'payment_type' => 'purchase',
                'payment_date' => now(),
                'amount' => $this->paymentAmount,
                'reference_number' => $this->paymentReference,
                'notes' => $this->paymentNotes,
                'created_by' => auth()->id(),
            ]);

            // Update purchase paid amount
            $purchase->paid_amount += $this->paymentAmount;

            // Calculate new due amount
            $newDueAmount = $purchase->total_amount - $purchase->paid_amount;

            if ($newDueAmount <= 0) {
                $purchase->payment_status = 'paid';
            } else {
                $purchase->payment_status = 'partial';
            }

            $purchase->save();

            DB::commit();

            // Log activity
            logActivity(
                'payment_processed',
                $purchase,
                [
                    'paid_amount' => $oldPaidAmount,
                    'payment_status' => $oldPaymentStatus
                ],
                [
                    'paid_amount' => $purchase->paid_amount,
                    'payment_status' => $purchase->payment_status,
                    'payment_amount' => $this->paymentAmount,
                    'payment_method' => $this->paymentMethod,
                    'payment_reference' => $this->paymentReference,
                    'payment_id' => $payment->id
                ]
            );

            // Close modal and reset
            $this->showPaymentModal = false;
            $this->reset(['paymentAmount', 'paymentReference', 'paymentNotes', 'paymentPurchaseId']);
            
            // Refresh the purchase data
            $this->loadPurchase();

            $this->dispatch('notify', [
                'message' => 'Payment processed successfully',
                'type' => 'success'
            ]);

            // Dispatch refresh event for any other components
            $this->dispatch('refreshComponent');
            $this->dispatch('paymentProcessed');

        } catch (\Exception $e) {
            DB::rollBack();
            
            $this->dispatch('notify', [
                'message' => 'Error processing payment: ' . $e->getMessage(),
                'type' => 'error'
            ]);
            
            Log::error('Payment error: ' . $e->getMessage());
        }
    }

    private function getPaymentMethodId($methodName)
    {
        $method = PaymentMethod::where('slug', $methodName)->first();
        if (!$method) {
            $method = PaymentMethod::create([
                'name' => ucfirst(str_replace('-', ' ', $methodName)),
                'slug' => $methodName,
                'is_active' => true,
            ]);
        }
        return $method->id;
    }

    private function updatePurchasePaymentStatus($purchase)
    {
        $dueAmount = $purchase->total_amount - $purchase->paid_amount;

        if ($dueAmount <= 0) {
            $purchase->payment_status = 'paid';
        } elseif ($purchase->paid_amount > 0 && $dueAmount > 0) {
            $purchase->payment_status = 'partial';
        } else {
            $purchase->payment_status = 'pending';
        }

        $purchase->due_amount = $dueAmount;
        $purchase->save();
    }

    public function render()
    {
        return view('livewire.purchases.show', [
            'purchase' => $this->purchase,
        ])->layout('layouts.app');
    }
}