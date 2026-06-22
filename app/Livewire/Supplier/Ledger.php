<?php

namespace App\Livewire\Supplier;

use Livewire\Component;
use App\Models\Supplier;
use App\Models\Purchase;
use App\Models\Payment;

class Ledger extends Component
{
    public $supplierId;
    public $supplier;
    public $ledgerStartDate;
    public $ledgerEndDate;
    public $transactions = [];
    public $purchases = [];
    public $payments = [];

    public function mount($id)
    {
        $this->supplierId = $id;
        $this->ledgerStartDate = now()->startOfMonth()->format('Y-m-d');
        $this->ledgerEndDate = now()->format('Y-m-d');
        $this->loadSupplier();
        $this->loadLedger();
    }

    public function loadSupplier()
    {
        $this->supplier = Supplier::find($this->supplierId);

        if (!$this->supplier) {
            session()->flash('error', 'Supplier not found');
            return $this->redirectRoute('suppliers.index', navigate: true);
        }
    }

    public function updatedLedgerStartDate()
    {
        $this->loadLedger();
    }

    public function updatedLedgerEndDate()
    {
        $this->loadLedger();
    }

    public function loadLedger()
    {
        // Get purchases for the period
        $this->purchases = Purchase::where('supplier_id', $this->supplierId)
            ->when($this->ledgerStartDate, function ($query) {
                $query->whereDate('purchase_date', '>=', $this->ledgerStartDate);
            })
            ->when($this->ledgerEndDate, function ($query) {
                $query->whereDate('purchase_date', '<=', $this->ledgerEndDate);
            })
            ->orderBy('purchase_date', 'desc')
            ->get();

        // Get payments for the period
        $this->payments = Payment::where('supplier_id', $this->supplierId)
            ->where('payment_type', 'purchase')
            ->when($this->ledgerStartDate, function ($query) {
                $query->whereDate('payment_date', '>=', $this->ledgerStartDate);
            })
            ->when($this->ledgerEndDate, function ($query) {
                $query->whereDate('payment_date', '<=', $this->ledgerEndDate);
            })
            ->orderBy('payment_date', 'desc')
            ->get();

        // Combine and sort transactions
        $this->transactions = collect()
            ->merge($this->purchases->map(function($purchase) {
                return [
                    'date' => $purchase->purchase_date,
                    'type' => 'purchase',
                    'reference' => $purchase->purchase_number,
                    'debit' => $purchase->total_amount,
                    'credit' => 0,
                    'balance' => 0,
                    'notes' => $purchase->notes,
                ];
            }))
            ->merge($this->payments->map(function($payment) {
                return [
                    'date' => $payment->payment_date,
                    'type' => 'payment',
                    'reference' => $payment->payment_number,
                    'debit' => 0,
                    'credit' => $payment->amount,
                    'balance' => 0,
                    'notes' => $payment->notes,
                ];
            }))
            ->sortByDesc('date')
            ->values()
            ->toArray();

        // Calculate running balance
        $balance = 0;
        foreach (array_reverse($this->transactions) as &$transaction) {
            if ($transaction['type'] === 'purchase') {
                $balance += $transaction['debit'];
            } else {
                $balance -= $transaction['credit'];
            }
            $transaction['balance'] = $balance;
        }
        $this->transactions = array_reverse($this->transactions);
    }

    public function goBack()
    {
        return $this->redirectRoute('suppliers.index', navigate: true);
    }

    public function render()
    {
        return view('livewire.suppliers.ledger', [
            'supplier' => $this->supplier,
            'purchases' => $this->purchases,
            'payments' => $this->payments,
            'transactions' => $this->transactions,
        ])->layout('layouts.app');
    }
}