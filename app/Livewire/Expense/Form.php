<?php

namespace App\Livewire\Expense;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Form extends Component
{
    use WithFileUploads;

    public $id = null;
    public $expense = null;

    // Form Fields
    public $expense_number = '';
    public $expense_category_id = '';
    public $expense_date;
    public $amount = 0;
    public $payment_method_id = '';
    public $reference_number = '';
    public $description = '';
    public $notes = '';
    public $receipt_image = null;
    public $existing_receipt = null;

    protected function rules()
    {
        $rules = [
            'expense_category_id' => 'required|exists:expense_categories,id',
            'expense_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'reference_number' => 'nullable|string|max:255',
            'description' => 'required|string|max:500',
            'notes' => 'nullable|string',
            'receipt_image' => 'nullable|image|max:2048', // 2MB max
        ];

        // Add unique rule for expense_number only when creating
        if (!$this->id) {
            $rules['expense_number'] = 'required|unique:expenses,expense_number';
        }

        return $rules;
    }

    public function mount($id = null)
    {
        $this->id = $id;
        $this->expense_date = now()->format('Y-m-d');

        if ($this->id) {
            $this->loadExpense();
        } else {
            $this->generateExpenseNumber();
        }
    }

    public function generateExpenseNumber()
    {
        $this->expense_number = 'EXP-' . date('Ymd') . '-' . str_pad(Expense::count() + 1, 4, '0', STR_PAD_LEFT);
    }

    public function loadExpense()
    {
        $this->expense = Expense::find($this->id);

        if ($this->expense) {
            $this->expense_number = $this->expense->expense_number;
            $this->expense_category_id = $this->expense->expense_category_id;
            $this->expense_date = $this->expense->expense_date->format('Y-m-d');
            $this->amount = $this->expense->amount;
            $this->payment_method_id = $this->expense->payment_method_id;
            $this->reference_number = $this->expense->reference_number;
            $this->description = $this->expense->description;
            $this->notes = $this->expense->notes;
            $this->existing_receipt = $this->expense->receipt_image;
        }
    }

    public function saveExpense()
    {
        $validatedData = $this->validate();

        DB::beginTransaction();

        try {
            $data = [
                'expense_number' => $this->expense_number,
                'expense_category_id' => $this->expense_category_id,
                'expense_date' => $this->expense_date,
                'amount' => $this->amount,
                'payment_method_id' => $this->payment_method_id,
                'reference_number' => $this->reference_number,
                'description' => $this->description,
                'notes' => $this->notes,
            ];

            // Handle receipt image upload
            if ($this->receipt_image) {
                // Delete old receipt if exists
                if ($this->existing_receipt) {
                    Storage::disk('public')->delete($this->existing_receipt);
                }

                $path = $this->receipt_image->store('expenses/receipts', 'public');
                $data['receipt_image'] = $path;
            }

            if ($this->id) {
                $expense = Expense::find($this->id);
                $oldData = collect($expense->toArray())->except(['updated_at'])->toArray();
                $expense->update($data);

                DB::commit();

                logActivity('updated', $expense, $oldData, $data);

                $this->dispatch('notify', [
                    'message' => 'Expense updated successfully',
                    'type' => 'success'
                ]);

                return $this->redirectRoute('expenses.show', $expense->id, navigate: true);
            } else {
                $data['created_by'] = auth()->id();
                $expense = Expense::create($data);

                DB::commit();

                logActivity('created', $expense, [], $data);

                $this->dispatch('notify', [
                    'message' => 'Expense created successfully',
                    'type' => 'success'
                ]);

                return $this->redirectRoute('expenses.show', $expense->id, navigate: true);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            $this->dispatch('notify', [
                'message' => 'Error saving expense: ' . $e->getMessage(),
                'type' => 'error'
            ]);

            Log::error('Expense save error: ' . $e->getMessage());
        }
    }

    public function removeReceipt()
    {
        if ($this->existing_receipt) {
            Storage::disk('public')->delete($this->existing_receipt);
            $this->existing_receipt = null;
            
            if ($this->id) {
                Expense::where('id', $this->id)->update(['receipt_image' => null]);
            }
        }
    }

    public function render()
    {
        return view('livewire.expenses.form', [
            'categories' => ExpenseCategory::where('is_active', true)->orderBy('name')->get(),
            'paymentMethods' => PaymentMethod::where('is_active', true)->orderBy('name')->get(),
        ])->layout('layouts.app');
    }
}