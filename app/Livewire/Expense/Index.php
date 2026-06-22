<?php

namespace App\Livewire\Expense;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\PaymentMethod;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $categoryFilter = '';
    public $paymentMethodFilter = '';
    public $startDate;
    public $endDate;
    public $minAmount = '';
    public $maxAmount = '';
    public $sortField = 'expense_date';
    public $sortDirection = 'desc';
    public $perPage = 15;

    public $selectedExpenses = [];
    public $selectAll = false;
    public $showBulkDeleteModal = false;
    public $showDeleteModal = false;
    public $deleteId = null;

    public $stats = [];
    public $dateRange = 'month';

    protected $queryString = [
        'search' => ['except' => ''],
        'categoryFilter' => ['except' => ''],
        'paymentMethodFilter' => ['except' => ''],
        'sortField' => ['except' => 'expense_date'],
        'sortDirection' => ['except' => 'desc'],
        'startDate' => ['except' => ''],
        'endDate' => ['except' => ''],
        'dateRange' => ['except' => 'month'],
    ];

    public function mount()
    {
        $this->setDateRange();
        $this->calculateStats();
    }

    public function setDateRange()
    {
        switch ($this->dateRange) {
            case 'today':
                $this->startDate = now()->startOfDay()->format('Y-m-d');
                $this->endDate = now()->endOfDay()->format('Y-m-d');
                break;
            case 'week':
                $this->startDate = now()->startOfWeek()->format('Y-m-d');
                $this->endDate = now()->endOfWeek()->format('Y-m-d');
                break;
            case 'month':
                $this->startDate = now()->startOfMonth()->format('Y-m-d');
                $this->endDate = now()->endOfMonth()->format('Y-m-d');
                break;
            case 'quarter':
                $this->startDate = now()->startOfQuarter()->format('Y-m-d');
                $this->endDate = now()->endOfQuarter()->format('Y-m-d');
                break;
            case 'year':
                $this->startDate = now()->startOfYear()->format('Y-m-d');
                $this->endDate = now()->endOfYear()->format('Y-m-d');
                break;
            case 'all':
                $this->startDate = null;
                $this->endDate = null;
                break;
        }
    }

    public function updatedDateRange()
    {
        $this->setDateRange();
        $this->calculateStats();
        $this->resetPage();
    }

    public function calculateStats()
    {
        $query = Expense::query();
        
        if ($this->startDate && $this->endDate) {
            $query->whereBetween('expense_date', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay()
            ]);
        }

        $this->stats = [
            'total_expenses' => $query->count(),
            'total_amount' => $query->sum('amount'),
            'average_amount' => $query->avg('amount'),
            'max_amount' => $query->max('amount'),
            'min_amount' => $query->min('amount'),
            'by_category' => ExpenseCategory::withCount(['expenses' => function ($q) {
                if ($this->startDate && $this->endDate) {
                    $q->whereBetween('expense_date', [
                        Carbon::parse($this->startDate)->startOfDay(),
                        Carbon::parse($this->endDate)->endOfDay()
                    ]);
                }
            }])->withSum(['expenses' => function ($q) {
                if ($this->startDate && $this->endDate) {
                    $q->whereBetween('expense_date', [
                        Carbon::parse($this->startDate)->startOfDay(),
                        Carbon::parse($this->endDate)->endOfDay()
                    ]);
                }
            }], 'amount')->get(),
        ];
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedCategoryFilter()
    {
        $this->resetPage();
        $this->calculateStats();
    }

    public function updatedPaymentMethodFilter()
    {
        $this->resetPage();
        $this->calculateStats();
    }

    public function updatedStartDate()
    {
        $this->dateRange = 'custom';
        $this->resetPage();
        $this->calculateStats();
    }

    public function updatedEndDate()
    {
        $this->dateRange = 'custom';
        $this->resetPage();
        $this->calculateStats();
    }

    public function updatedMinAmount()
    {
        $this->resetPage();
    }

    public function updatedMaxAmount()
    {
        $this->resetPage();
    }

    public function getExpensesQuery()
    {
        return Expense::with(['category', 'paymentMethod', 'creator'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('expense_number', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%')
                        ->orWhere('notes', 'like', '%' . $this->search . '%')
                        ->orWhere('reference_number', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->categoryFilter, fn($q) => $q->where('expense_category_id', $this->categoryFilter))
            ->when($this->paymentMethodFilter, fn($q) => $q->where('payment_method_id', $this->paymentMethodFilter))
            ->when($this->minAmount, fn($q) => $q->where('amount', '>=', $this->minAmount))
            ->when($this->maxAmount, fn($q) => $q->where('amount', '<=', $this->maxAmount))
            ->when($this->startDate && $this->endDate, function ($query) {
                $query->whereBetween('expense_date', [
                    Carbon::parse($this->startDate)->startOfDay(),
                    Carbon::parse($this->endDate)->endOfDay()
                ]);
            });
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

   public function getCurrentPageIds()
    {
        return $this->getExpensesQuery()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage)
            ->pluck('id')
            ->map(fn($id) => (string) $id)
            ->toArray();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedExpenses = $this->getCurrentPageIds();
        } else {
            $this->selectedExpenses = [];
        }
    }

    public function updatedSelectedEselectedExpenses()
    {
        $currentPageIds = $this->getCurrentPageIds();
        $selectedIds = $this->selectedExpenses;

        sort($currentPageIds);
        sort($selectedIds);
        $this->selectAll = $currentPageIds === $selectedIds && !empty($currentPageIds);
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        $expense = Expense::find($this->deleteId);
        
        if ($expense) {
            // Delete receipt if exists
            if ($expense->receipt_image) {
                Storage::disk('public')->delete($expense->receipt_image);
            }
            
            $expense->delete();
            
            $this->dispatch('notify', [
                'message' => 'Expense deleted successfully',
                'type' => 'success'
            ]);
            
            $this->calculateStats();
        }
        
        $this->showDeleteModal = false;
        $this->deleteId = null;
    }

    public function confirmBulkDelete()
    {
        $this->showBulkDeleteModal = true;
    }

    public function bulkDelete()
    {
        if (empty($this->selectedExpenses)) {
            $this->dispatch('notify', [
                'message' => 'No expenses selected',
                'type' => 'error'
            ]);
            return;
        }

        DB::beginTransaction();

        try {
            $expenses = Expense::whereIn('id', $this->selectedExpenses)->get();

            // Delete receipt images
            foreach ($expenses as $expense) {
                if ($expense->receipt_image) {
                    Storage::disk('public')->delete($expense->receipt_image);
                }
            }

            Expense::whereIn('id', $this->selectedExpenses)->delete();

            DB::commit();

            $this->selectedExpenses = [];
            $this->selectAll = false;
            $this->showBulkDeleteModal = false;

            $this->dispatch('notify', [
                'message' => 'Selected expenses deleted successfully',
                'type' => 'success'
            ]);

            $this->calculateStats();

        } catch (\Exception $e) {
            DB::rollBack();

            $this->dispatch('notify', [
                'message' => 'Error deleting expenses: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.expenses.index', [
            'expenses' => $this->getExpensesQuery()
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate($this->perPage),
            'categories' => ExpenseCategory::orderBy('name')->get(),
            'paymentMethods' => PaymentMethod::orderBy('name')->get(),
        ])->layout('layouts.app');
    }
}