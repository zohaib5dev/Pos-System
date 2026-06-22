<div>
    <!-- ========== PAGE HEADER ========== -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h4 class="mb-0 fw-bold d-flex align-items-center gap-2">
                <i class="bi bi-clock-history"></i> Log Details
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('activity-logs.index') }}" class="text-decoration-none">Activity Logs</a></li>
                    <li class="breadcrumb-item active">#{{ $log->id }}</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('activity-logs.index') }}" class="btn btn-secondary btn-sm shadow-sm">
            <i class="bi bi-arrow-left"></i> <span class="d-none d-sm-inline">Back to List</span>
            <span class="d-sm-none">Back</span>
        </a>
    </div>

    @if($log)
    <!-- ========== LOG HEADER ========== -->
    <div class="card border-0 shadow-lg mb-3">
        <div class="card-body p-3 p-sm-4">
            <div class="d-flex flex-wrap align-items-center gap-3">
                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" 
                     style="width: 56px; height: 56px;">
                    <i class="bi bi-clock-history fs-3 text-white"></i>
                </div>
                <div class="flex-grow-1">
                    <h4 class="mb-0 fw-bold">Log #{{ $log->id }}</h4>
                    <div class="d-flex flex-wrap gap-2 mt-1">
                        <span class="text-muted small">
                            <i class="bi bi-calendar3 me-1"></i> {{ $log->created_at }}
                        </span>
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
                <div class="text-end">
                    <div class="text-muted small">Model</div>
                    <div class="fw-semibold">
                        @php
                            $parts = explode('\\', $log->model_type);
                            $shortName = end($parts);
                        @endphp
                        {{ $shortName }}
                    </div>
                    <div class="text-muted small">ID: {{ $log->model_id }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========== BASIC INFORMATION ========== -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-transparent border-0 pt-3">
            <h6 class="mb-0 fw-bold text-info d-flex align-items-center gap-2">
                <i class="bi bi-info-circle"></i> Basic Information
            </h6>
        </div>
        <div class="card-body pt-0">
            <!-- Mobile View -->
            <div class="d-md-none">
                <div class="d-flex flex-column gap-1">
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted small">User</span>
                        <span class="fw-semibold">{{ $log->user->name ?? 'System' }}</span>
                    </div>
                    @if($log->user)
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted small">Email</span>
                        <span>{{ $log->user->email }}</span>
                    </div>
                    @endif
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted small">Model Type</span>
                        <span>
                            @php
                                $parts = explode('\\', $log->model_type);
                                $shortName = end($parts);
                            @endphp
                            {{ $shortName }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted small">Model ID</span>
                        <span>{{ $log->model_id }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted small">IP Address</span>
                        <span>{{ $log->ip_address ?? '-' }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1">
                        <span class="text-muted small">User Agent</span>
                        <span class="text-end small text-break">{{ $log->user_agent ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <!-- Desktop View -->
            <div class="d-none d-md-block">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="p-2 bg-light-soft rounded-3">
                            <span class="text-muted small d-block">User</span>
                            <span class="fw-semibold">{{ $log->user->name ?? 'System' }}</span>
                            @if($log->user)
                                <div class="text-muted small">{{ $log->user->email }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-2 bg-light-soft rounded-3">
                            <span class="text-muted small d-block">Model</span>
                            <span class="fw-semibold">
                                @php
                                    $parts = explode('\\', $log->model_type);
                                    $shortName = end($parts);
                                @endphp
                                {{ $shortName }}
                            </span>
                            <div class="text-muted small">ID: {{ $log->model_id }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-2 bg-light-soft rounded-3">
                            <span class="text-muted small d-block">IP Address</span>
                            <span>{{ $log->ip_address ?? '-' }}</span>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="p-2 bg-light-soft rounded-3">
                            <span class="text-muted small d-block">User Agent</span>
                            <span class="small text-break">{{ $log->user_agent ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========== CHANGES ========== -->
    @if(!empty($oldValues) || !empty($newValues))
    <div class="row g-3">
        <!-- Old Values -->
        @if(!empty($oldValues))
        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 pt-3">
                    <h6 class="mb-0 fw-bold text-danger d-flex align-items-center gap-2">
                        <i class="bi bi-arrow-left-circle"></i> Previous Values
                    </h6>
                </div>
                <div class="card-body pt-0">
                    <div class="d-flex flex-column gap-1">
                        @foreach($oldValues as $key => $value)
                            @if(!in_array($key, ['id', 'created_at', 'updated_at', 'deleted_at']))
                                <div class="d-flex justify-content-between py-1 border-bottom">
                                    <span class="text-muted small text-capitalize">{{ str_replace('_', ' ', $key) }}</span>
                                    <span>
                                        @if(is_array($value) || is_object($value))
                                            <code class="bg-dark text-light px-2 py-1 rounded small d-block text-break" style="font-size: 0.65rem; max-width: 200px;">{{ json_encode($value) }}</code>
                                        @elseif(is_bool($value))
                                            {{ $value ? 'Yes' : 'No' }}
                                        @elseif(is_null($value))
                                            <span class="text-muted">null</span>
                                        @else
                                            {{ $value }}
                                        @endif
                                    </span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- New Values -->
        @if(!empty($newValues))
        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 pt-3">
                    <h6 class="mb-0 fw-bold text-success d-flex align-items-center gap-2">
                        <i class="bi bi-arrow-right-circle"></i> New Values
                    </h6>
                </div>
                <div class="card-body pt-0">
                    <div class="d-flex flex-column gap-1">
                        @foreach($newValues as $key => $value)
                            @if(!in_array($key, ['id', 'created_at', 'updated_at', 'deleted_at']))
                                <div class="d-flex justify-content-between py-1 border-bottom">
                                    <span class="text-muted small text-capitalize">{{ str_replace('_', ' ', $key) }}</span>
                                    <span>
                                        @if(is_array($value) || is_object($value))
                                            <code class="bg-dark text-light px-2 py-1 rounded small d-block text-break" style="font-size: 0.65rem; max-width: 200px;">{{ json_encode($value) }}</code>
                                        @elseif(is_bool($value))
                                            {{ $value ? 'Yes' : 'No' }}
                                        @elseif(is_null($value))
                                            <span class="text-muted">null</span>
                                        @else
                                            {{ $value }}
                                        @endif
                                    </span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
    @endif

    <!-- ========== RAW DATA ========== -->
    <div class="card border-0 shadow-sm mt-3">
        <div class="card-header bg-transparent border-0 pt-3">
            <h6 class="mb-0 fw-bold text-secondary d-flex align-items-center gap-2">
                <i class="bi bi-code-square"></i> Raw Data
            </h6>
        </div>
        <div class="card-body pt-0">
            <div class="bg-dark rounded-3 p-3" style="overflow-x: auto;">
                <pre class="text-light mb-0" style="font-size: 0.7rem; font-family: 'Courier New', monospace;">{{ json_encode($log, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
            </div>
        </div>
    </div>

    @else
    <!-- ========== NOT FOUND ========== -->
    <div class="card border-0 shadow-lg">
        <div class="card-body text-center py-5">
            <i class="bi bi-exclamation-circle fs-1 d-block mb-3 text-danger"></i>
            <h4 class="text-danger">Log Not Found</h4>
            <p class="text-muted">The requested activity log could not be found.</p>
            <a href="{{ route('activity-logs.index') }}" class="btn btn-primary btn-sm shadow-sm mt-2">
                <i class="bi bi-arrow-left"></i> Back to List
            </a>
        </div>
    </div>
    @endif

 
</div>