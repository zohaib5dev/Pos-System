<?php

namespace App\Livewire\ActivityLog;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ActivityLog;
use App\Models\User;
use Carbon\Carbon;

class Index extends Component
{
    use WithPagination;

    // State
    public $selectedLogs = [];
    public $selectAll = false;
    public $showDeleteModal = false;
    public $showClearModal = false;
    public $logId = null;

    // Filters
    public $search = '';
    public $userFilter = '';
    public $actionFilter = '';
    public $modelFilter = '';
    public $dateRange = 'all';
    public $startDate;
    public $endDate;
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'userFilter' => ['except' => ''],
        'actionFilter' => ['except' => ''],
        'modelFilter' => ['except' => ''],
        'dateRange' => ['except' => 'all'],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function mount()
    {
        $this->startDate = now()->subDays(30)->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    public function getStatsProperty()
    {
        return [
            'total_logs' => ActivityLog::count(),
            'today_logs' => ActivityLog::whereDate('created_at', now())->count(),
            'unique_users' => ActivityLog::distinct('user_id')->count('user_id'),
            'unique_actions' => ActivityLog::distinct('action')->count('action'),
            'oldest_log' => ActivityLog::min('created_at'),
            'newest_log' => ActivityLog::max('created_at'),
        ];
    }

    public function getActionsProperty()
    {
        return ActivityLog::distinct('action')
            ->pluck('action')
            ->map(function($action) {
                return [
                    'value' => $action,
                    'label' => ucwords(str_replace('_', ' ', $action))
                ];
            })
            ->toArray();
    }

    public function getModelsProperty()
    {
        return ActivityLog::distinct('model_type')
            ->pluck('model_type')
            ->map(function($model) {
                $parts = explode('\\', $model);
                $shortName = end($parts);
                return [
                    'value' => $model,
                    'label' => $shortName
                ];
            })
            ->toArray();
    }

    public function getUsersProperty()
    {
        return User::select('id', 'name')
            ->whereIn('id', ActivityLog::distinct('user_id')->pluck('user_id'))
            ->orderBy('name')
            ->get();
    }

    public function viewLog($id)
    {
        return redirect()->route('activity-logs.show', $id);
    }

    public function confirmDelete($id)
    {
        $this->logId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteLog()
    {
        $log = ActivityLog::find($this->logId);

        if (!$log) {
            $this->showDeleteModal = false;
            $this->dispatch('notify', [
                'message' => 'Log not found',
                'type' => 'error'
            ]);
            return;
        }

        $logData = collect($log->toArray())->except(['updated_at'])->toArray();
        $log->delete();
        $this->showDeleteModal = false;

        logActivity('deleted', new ActivityLog(), $logData, []);

        $this->dispatch('notify', [
            'message' => 'Log deleted successfully',
            'type' => 'success'
        ]);
    }

    public function confirmClearAll()
    {
        $this->showClearModal = true;
    }

    public function clearAllLogs()
    {
        $count = ActivityLog::count();
        ActivityLog::truncate();
        $this->showClearModal = false;

        logActivity('cleared_all', new ActivityLog(), ['count' => $count], []);

        $this->dispatch('notify', [
            'message' => 'All logs cleared successfully',
            'type' => 'success'
        ]);
    }

    public function clearOlderThan($days = 30)
    {
        $date = now()->subDays($days);
        $count = ActivityLog::where('created_at', '<', $date)->delete();

        logActivity('cleared_older', new ActivityLog(), ['days' => $days, 'count' => $count], []);

        $this->dispatch('notify', [
            'message' => "Deleted {$count} logs older than {$days} days",
            'type' => 'success'
        ]);
    }

    public function getCurrentPageIds()
    {
        return $this->getLogsQuery()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage)
            ->pluck('id')
            ->map(fn($id) => (string) $id)
            ->toArray();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedLogs = $this->getCurrentPageIds();
        } else {
            $this->selectedLogs = [];
        }
    }

    public function updatedSelectedselectedLogs()
    {
        $currentPageIds = $this->getCurrentPageIds();
        $selectedIds = $this->selectedLogs;

        sort($currentPageIds);
        sort($selectedIds);
        $this->selectAll = $currentPageIds === $selectedIds && !empty($currentPageIds);
    }

    public function bulkDelete()
    {
        if (empty($this->selectedLogs)) {
            $this->dispatch('notify', [
                'message' => 'No logs selected',
                'type' => 'error'
            ]);
            return;
        }

        $count = count($this->selectedLogs);
        $logs = ActivityLog::whereIn('id', $this->selectedLogs)->get();
        $logIds = $logs->pluck('id')->toArray();
        
        $logsData = $logs->map(function($log) {
            return collect($log->toArray())->except(['updated_at'])->toArray();
        })->toArray();

        ActivityLog::whereIn('id', $this->selectedLogs)->delete();

        $this->selectedLogs = [];
        $this->selectAll = false;

        logActivity('bulk_deleted', new ActivityLog(), ['log_ids' => $logIds, 'logs' => $logsData], []);

        $this->dispatch('notify', [
            'message' => "Deleted {$count} logs successfully",
            'type' => 'success'
        ]);
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

    public function exportLogs()
    {
        $logs = $this->getLogsQuery()->get();

        logActivity('exported', new ActivityLog(), [], [
            'filter' => [
                'search' => $this->search,
                'user' => $this->userFilter,
                'action' => $this->actionFilter,
                'model' => $this->modelFilter,
                'date_range' => $this->dateRange,
                'start_date' => $this->startDate,
                'end_date' => $this->endDate,
            ],
            'count' => $logs->count()
        ]);

        $filename = 'activity-logs-' . now()->format('Y-m-d-H-i-s') . '.csv';
        $handle = fopen('php://memory', 'r+');

        // Headers
        fputcsv($handle, [
            'ID', 'User', 'Action', 'Model Type', 'Model ID',
            'IP Address', 'User Agent', 'Created At', 'Old Values', 'New Values'
        ]);

        // Data
        foreach ($logs as $log) {
            fputcsv($handle, [
                $log->id,
                $log->user->name ?? 'System',
                $log->action,
                $log->model_type,
                $log->model_id,
                $log->ip_address,
                $log->user_agent,
                $log->created_at,
                json_encode(json_decode($log->old_values ?? '{}', true)),
                json_encode(json_decode($log->new_values ?? '{}', true)),
            ]);
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return response()->streamDownload(function () use ($content) {
            echo $content;
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function getLogsQuery()
    {
        return ActivityLog::with('user')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('action', 'like', '%' . $this->search . '%')
                      ->orWhere('model_type', 'like', '%' . $this->search . '%')
                      ->orWhere('ip_address', 'like', '%' . $this->search . '%')
                      ->orWhereHas('user', function ($uq) {
                          $uq->where('name', 'like', '%' . $this->search . '%')
                             ->orWhere('email', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->when($this->userFilter, fn($q) => $q->where('user_id', $this->userFilter))
            ->when($this->actionFilter, fn($q) => $q->where('action', $this->actionFilter))
            ->when($this->modelFilter, fn($q) => $q->where('model_type', $this->modelFilter))
            ->when($this->dateRange !== 'all', function ($query) {
                switch ($this->dateRange) {
                    case 'today':
                        return $query->whereDate('created_at', now());
                    case 'yesterday':
                        return $query->whereDate('created_at', now()->subDay());
                    case 'week':
                        return $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    case 'month':
                        return $query->whereMonth('created_at', now()->month);
                    case 'custom':
                        return $query->whereBetween('created_at', [
                            Carbon::parse($this->startDate)->startOfDay(),
                            Carbon::parse($this->endDate)->endOfDay()
                        ]);
                }
            });
    }

    public function render()
    {
        return view('livewire.activity-logs.index', [
            'logs' => $this->getLogsQuery()
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate($this->perPage),
            'stats' => $this->stats,
            'users' => $this->users,
            'actions' => $this->actions,
            'models' => $this->models,
        ])->layout('layouts.app');
    }
}