<?php

namespace App\Livewire\Expense;

use Livewire\Component;
use App\Models\Expense;
use Illuminate\Support\Facades\Storage;

class Show extends Component
{
    public $id;
    public $expense = null;
    public $showDeleteModal = false;
    
    public function mount($id)
    {
        $this->id = $id;
        $this->loadExpense();
    }
    
    public function loadExpense()
    {
        $this->expense = Expense::with(['category', 'paymentMethod', 'creator'])
            ->find($this->id);
        
        if (!$this->expense) {
            $this->dispatch('notify', [
                'message' => 'Expense not found',
                'type' => 'error'
            ]);
            
            return $this->redirectRoute('expenses.index', navigate: true);
        }
    }
    
    public function confirmDelete()
    {
        $this->showDeleteModal = true;
    }
    
    public function deleteExpense()
    {
        if (!$this->expense) {
            $this->showDeleteModal = false;
            
            $this->dispatch('notify', [
                'message' => 'Expense not found',
                'type' => 'error'
            ]);
            return;
        }
        
        try {
            $expenseData = collect($this->expense->toArray())->except(['updated_at'])->toArray();
            
            // Delete receipt if exists
            if ($this->expense->receipt_image) {
                Storage::disk('public')->delete($this->expense->receipt_image);
            }
            
            $this->expense->delete();
            $this->showDeleteModal = false;
            
            logActivity('deleted', $this->expense, $expenseData, []);
            
            $this->dispatch('notify', [
                'message' => 'Expense deleted successfully',
                'type' => 'success'
            ]);
            
            return $this->redirectRoute('expenses.index', navigate: true);
            
        } catch (\Exception $e) {
            $this->showDeleteModal = false;
            $this->dispatch('notify', [
                'message' => 'Error deleting expense: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }
    
    public function viewReceipt()
    {
        if ($this->expense && $this->expense->receipt_image) {
            logActivity('viewed_receipt', $this->expense, [], ['receipt' => $this->expense->receipt_image]);
            
            return response()->file(storage_path('app/public/' . $this->expense->receipt_image));
        }
        
        $this->dispatch('notify', [
            'message' => 'Receipt not found',
            'type' => 'error'
        ]);
    }
    
    public function downloadReceipt()
    {
        if ($this->expense && $this->expense->receipt_image) {
            logActivity('downloaded_receipt', $this->expense, [], ['receipt' => $this->expense->receipt_image]);
            
            return Storage::disk('public')->download($this->expense->receipt_image);
        }
        
        $this->dispatch('notify', [
            'message' => 'Receipt not found',
            'type' => 'error'
        ]);
    }
    
    public function render()
    {
        return view('livewire.expenses.show', [
            'expense' => $this->expense,
        ])->layout('layouts.app');
    }
}