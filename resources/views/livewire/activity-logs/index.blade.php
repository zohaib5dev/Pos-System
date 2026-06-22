<div>
    <!-- ========== PAGE HEADER ========== -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h4 class="mb-0 fw-bold d-flex align-items-center gap-2">
                <i class="bi bi-clock-history"></i> Activity Logs
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active">Activity Logs</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex flex-wrap gap-1">
            <button wire:click="exportLogs" class="btn btn-success btn-sm shadow-sm">
                <i class="bi bi-download"></i> <span class="d-none d-sm-inline">Export</span>
            </button>
            <button wire:click="confirmClearAll"
                    wire:confirm="Are you sure you want to clear ALL logs? This action cannot be undone."
                    class="btn btn-danger btn-sm shadow-sm">
                <i class="bi bi-trash"></i> <span class="d-none d-sm-inline">Clear All</span>
            </button>
        </div>
    </div>

    <!-- ========== STATS CARDS ========== -->
    <div class="row g-2 g-sm-3 mb-3">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm stat-card stat-card-info">
                <div class="card-body p-2 p-sm-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="mb-0 fw-bold text-info">{{ number_format($stats['total_logs']) }}</h5>
                            <p class="mb-0 text-muted small">Total Logs</p>
                        </div>
                        <div class="bg-info-soft rounded-3 p-2">
                            <i class="bi bi-clock-history fs-4 text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm stat-card stat-card-primary">
                <div class="card-body p-2 p-sm-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="mb-0 fw-bold text-primary">{{ number_format($stats['today_logs']) }}</h5>
                            <p class="mb-0 text-muted small">Today</p>
                        </div>
                        <div class="bg-primary-soft rounded-3 p-2">
                            <i class="bi bi-calendar-day fs-4 text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm stat-card stat-card-warning">
                <div class="card-body p-2 p-sm-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="mb-0 fw-bold text-warning">{{ number_format($stats['unique_users']) }}</h5>
                            <p class="mb-0 text-muted small">Unique Users</p>
                        </div>
                        <div class="bg-warning-soft rounded-3 p-2">
                            <i class="bi bi-people fs-4 text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm stat-card stat-card-secondary">
                <div class="card-body p-2 p-sm-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="mb-0 fw-bold text-secondary">{{ number_format($stats['unique_actions']) }}</h5>
                            <p class="mb-0 text-muted small">Unique Actions</p>
                        </div>
                        <div class="bg-secondary-soft rounded-3 p-2">
                            <i class="bi bi-tags fs-4 text-secondary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========== FILTERS ========== -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-2 p-sm-3">
            <div class="row g-2">
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-search"></i></span>
                        <input type="text"
                            wire:model.live.debounce.300ms="search"
                            class="form-control form-control-sm border-0 bg-light"
                            placeholder="Search logs...">
                    </div>
                </div>
                <div class="col-6 col-sm-3 col-md-2">
                    <select wire:model.live="userFilter" class="form-select form-select-sm bg-light border-0">
                        <option value="">All Users</option>
                        @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-sm-3 col-md-2">
                    <select wire:model.live="actionFilter" class="form-select form-select-sm bg-light border-0">
                        <option value="">All Actions</option>
                        @foreach($actions as $action)
                        <option value="{{ $action['value'] }}">{{ $action['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-sm-3 col-md-2">
                    <select wire:model.live="modelFilter" class="form-select form-select-sm bg-light border-0">
                        <option value="">All Models</option>
                        @foreach($models as $model)
                        <option value="{{ $model['value'] }}">{{ $model['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-sm-3 col-md-2">
                    <select wire:model.live="dateRange" class="form-select form-select-sm bg-light border-0">
                        <option value="all">All Time</option>
                        <option value="today">Today</option>
                        <option value="yesterday">Yesterday</option>
                        <option value="week">This Week</option>
                        <option value="month">This Month</option>
                        <option value="custom">Custom</option>
                    </select>
                </div>
            </div>

            <!-- Custom Date Range -->
            @if($dateRange === 'custom')
            <div class="row g-2 mt-2">
                <div class="col-6 col-sm-6 col-md-3">
                    <label class="form-label small fw-semibold">Start Date</label>
                    <input type="date" wire:model.live="startDate" class="form-control form-control-sm bg-light border-0">
                </div>
                <div class="col-6 col-sm-6 col-md-3">
                    <label class="form-label small fw-semibold">End Date</label>
                    <input type="date" wire:model.live="endDate" class="form-control form-control-sm bg-light border-0">
                </div>
            </div>
            @endif

            <!-- Quick Actions -->
            <div class="mt-2 d-flex flex-wrap gap-1">
                <button wire:click="clearOlderThan(30)"
                    wire:confirm="Delete logs older than 30 days?"
                    class="btn btn-warning btn-sm shadow-sm">
                    <i class="bi bi-trash"></i> >30 days
                </button>
                <button wire:click="clearOlderThan(60)"
                    wire:confirm="Delete logs older than 60 days?"
                    class="btn btn-warning btn-sm shadow-sm">
                    <i class="bi bi-trash"></i> >60 days
                </button>
                <button wire:click="clearOlderThan(90)"
                    wire:confirm="Delete logs older than 90 days?"
                    class="btn btn-warning btn-sm shadow-sm">
                    <i class="bi bi-trash"></i> >90 days
                </button>
            </div>
        </div>
    </div>

    <!-- ========== BULK ACTIONS ========== -->
    @if(count($selectedLogs) > 0)
    <div class="alert alert-info d-flex flex-wrap align-items-center justify-content-between gap-2 py-2 px-3">
        <span class="small"><i class="bi bi-check2-square me-1"></i><strong>{{ count($selectedLogs) }}</strong> selected</span>
        <div class="d-flex gap-1">
            <button wire:click="bulkDelete"
                wire:confirm="Delete selected logs?"
                class="btn btn-danger btn-sm shadow-sm">
                <i class="bi bi-trash"></i> <span class="d-none d-sm-inline">Delete</span>
            </button>
            <button wire:click="$set('selectedLogs', [])" class="btn btn-secondary btn-sm shadow-sm">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    </div>
    @endif

    <!-- ========== LOGS TABLE ========== -->
    <div class="card border-0 shadow-lg">
        <div class="card-header bg-transparent border-0 d-flex flex-wrap align-items-center justify-content-between pt-3">
            <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
                <i class="bi bi-list-ul"></i> Activity Logs
            </h5>
            <span class="badge bg-primary-soft text-primary rounded-pill">{{ $logs->total() }}</span>
        </div>

        <div class="card-body pt-0">
            <!-- ========== MOBILE CARD VIEW ========== -->
            <div class="d-md-none">
                @forelse($logs as $log)
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-start justify-content-between mb-2">
                            <div class="d-flex align-items-center gap-2 flex-grow-1">
                                <input type="checkbox" 
                                    wire:model.live="selectedLogs" 
                                    value="{{ $log->id }}" 
                                    class="form-check-input mt-0">
                                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" 
                                     style="width: 32px; height: 32px;">
                                    <span class="text-white fw-bold" style="font-size: 0.65rem;">
                                        {{ $log->user ? substr($log->user->name, 0, 2) : 'SY' }}
                                    </span>
                                </div>
                                <div>
                                    <div class="fw-semibold small">{{ $log->user->name ?? 'System' }}</div>
                                    <div class="text-muted small">{{ $log->created_at }}</div>
                                </div>
                            </div>
                            <div>
                                @if($log->action === 'created')
                                <span class="badge bg-success-soft text-success rounded-pill">Created</span>
                                @elseif($log->action === 'updated')
                                <span class="badge bg-primary-soft text-primary rounded-pill">Updated</span>
                                @elseif($log->action === 'deleted')
                                <span class="badge bg-danger-soft text-danger rounded-pill">Deleted</span>
                                @else
                                <span class="badge bg-secondary-soft text-secondary rounded-pill">{{ ucwords(str_replace('_', ' ', $log->action)) }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="row g-1 small mb-2">
                            <div class="col-6">
                                <span class="text-muted">Model:</span>
                                @php
                                $parts = explode('\\', $log->model_type);
                                $shortName = end($parts);
                                @endphp
                                <span>{{ $shortName }}</span>
                            </div>
                            <div class="col-6">
                                <span class="text-muted">ID:</span>
                                <span>{{ $log->model_id }}</span>
                            </div>
                            <div class="col-12">
                                <span class="text-muted">IP:</span>
                                <span>{{ $log->ip_address ?? '-' }}</span>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-1 pt-2 border-top">
                            <a href="{{ route('activity-logs.show', $log->id) }}"
                                class="btn btn-info-soft text-info btn-sm rounded-circle shadow-sm" title="View" >
                                <i class="bi bi-eye"></i>
                            </a>
                            <button wire:click="confirmDelete({{ $log->id }})"
                                class="btn btn-danger-soft text-danger btn-sm rounded-circle shadow-sm" title="Delete" >
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-5">
                    <i class="bi bi-clock-history fs-1 d-block mb-3 text-muted"></i>
                    <p class="text-muted">No activity logs found</p>
                    <p class="text-muted small">Try adjusting your filters</p>
                </div>
                @endforelse
            </div>

            <!-- ========== DESKTOP TABLE VIEW ========== -->
            <div class="table-responsive d-none d-md-block">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead class="table">
                        <tr>
                            <th width="40px">
                                <input type="checkbox" wire:model.live="selectAll" class="form-check-input">
                            </th>
                            <th wire:click="sortBy('created_at')" style="cursor: pointer" class="small">
                                Date & Time
                                @if($sortField === 'created_at')
                                <i class="bi bi-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th wire:click="sortBy('user_id')" style="cursor: pointer" class="small">
                                User
                                @if($sortField === 'user_id')
                                <i class="bi bi-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th class="small">Action</th>
                            <th class="small">Model</th>
                            <th class="small">Model ID</th>
                            <th class="small">IP Address</th>
                            <th class="text-center small" width="100px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td>
                                <input type="checkbox" wire:model.live="selectedLogs" value="{{ $log->id }}" class="form-check-input">
                            </td>
                            <td class="text-nowrap small">{{ $log->created_at }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                        style="width: 32px; height: 32px;">
                                        <span class="text-white fw-bold" style="font-size: 0.65rem;">
                                            {{ $log->user ? substr($log->user->name, 0, 2) : 'SY' }}
                                        </span>
                                    </div>
                                    <span>{{ $log->user->name ?? 'System' }}</span>
                                </div>
                            </td>
                            <td>
                                @if($log->action === 'created')
                                <span class="badge bg-success-soft text-success rounded-pill px-3 py-2">Created</span>
                                @elseif($log->action === 'updated')
                                <span class="badge bg-primary-soft text-primary rounded-pill px-3 py-2">Updated</span>
                                @elseif($log->action === 'deleted')
                                <span class="badge bg-danger-soft text-danger rounded-pill px-3 py-2">Deleted</span>
                                @else
                                <span class="badge bg-secondary-soft text-secondary rounded-pill px-3 py-2">{{ ucwords(str_replace('_', ' ', $log->action)) }}</span>
                                @endif
                            </td>
                            <td>
                                @php
                                $parts = explode('\\', $log->model_type);
                                $shortName = end($parts);
                                @endphp
                                <span class="fw-semibold">{{ $shortName }}</span>
                            </td>
                            <td>{{ $log->model_id }}</td>
                            <td><code class="bg-light px-2 py-1 rounded small">{{ $log->ip_address ?? '-' }}</code></td>
                            <td>
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ route('activity-logs.show', $log->id) }}"
                                        class="btn btn-info-soft text-info btn-sm rounded-circle shadow-sm" title="View" >
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <button wire:click="confirmDelete({{ $log->id }})"
                                        class="btn btn-danger-soft text-danger btn-sm rounded-circle shadow-sm" title="Delete" >
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="bi bi-clock-history fs-1 d-block mb-3 text-muted"></i>
                                <p class="text-muted">No activity logs found</p>
                                <p class="text-muted small">Try adjusting your filters</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- ========== PAGINATION ========== -->
            <div class="mt-3">
                {{ $logs->links('livewire::bootstrap') }}
            </div>
        </div>
    </div>

    <!-- ========== DELETE MODAL ========== -->
    @if($showDeleteModal)
    <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5);" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title text-danger d-flex align-items-center gap-2">
                        <i class="bi bi-exclamation-triangle-fill"></i> Delete Log
                    </h5>
                    <button type="button" class="btn-close" wire:click="$set('showDeleteModal', false)"></button>
                </div>
                <div class="modal-body text-center py-3">
                    <p class="mb-0">Delete this activity log?</p>
                    <p class="text-danger small"><i class="bi bi-info-circle me-1"></i>This cannot be undone.</p>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary btn-sm shadow-sm" wire:click="$set('showDeleteModal', false)">
                        Cancel
                    </button>
                    <button type="button" class="btn btn-danger btn-sm shadow-sm" wire:click="deleteLog">
                        <i class="bi bi-trash"></i> Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- ========== CLEAR ALL MODAL ========== -->
    @if($showClearModal)
    <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5);" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title text-danger d-flex align-items-center gap-2">
                        <i class="bi bi-exclamation-triangle-fill"></i> Clear All Logs
                    </h5>
                    <button type="button" class="btn-close" wire:click="$set('showClearModal', false)"></button>
                </div>
                <div class="modal-body text-center py-3">
                    <p class="mb-1">Delete ALL activity logs?</p>
                    <p class="text-danger small"><i class="bi bi-info-circle me-1"></i>{{ number_format($stats['total_logs']) }} logs will be permanently deleted.</p>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary btn-sm shadow-sm" wire:click="$set('showClearModal', false)">
                        Cancel
                    </button>
                    <button type="button" class="btn btn-danger btn-sm shadow-sm" wire:click="clearAllLogs">
                        <i class="bi bi-trash"></i> Clear All
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    

    <script>
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                @this.set('showDeleteModal', false);
                @this.set('showClearModal', false);
            }
        });
    </script>
</div>