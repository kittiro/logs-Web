@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="d-flex align-items-center">
                            <a href="{{ route('dashboard') }}" class="btn btn-primary btn-sm me-3">
                                <i class="fas fa-arrow-left me-1"></i> กลับไป Dashboard
                            </a>
                            <h5 class="mb-0">
                                <i class="fas fa-globe me-2"></i>
                                Web Access Logs - {{ $date ?? date('Y-m-d') }}
                            </h5>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('web-access-logs.download', ['date' => $date ?? date('Y-m-d')]) }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-download me-1"></i> Download
                            </a>
                            <button type="button" class="btn btn-outline-info btn-sm" onclick="loadStats()">
                                <i class="fas fa-chart-bar me-1"></i> Statistics
                            </button>
                        </div>
                    </div>
                </div>

                
                <div class="card-body">
                    <!-- Filters -->
                    <form method="GET" class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label for="date" class="form-label">Date</label>
                            <select name="date" id="date" class="form-select" onchange="this.form.submit()">
                                @foreach($availableDates as $availableDate)
                                    <option value="{{ $availableDate }}" {{ $date == $availableDate ? 'selected' : '' }}>
                                        {{ $availableDate }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" name="search" id="search" class="form-control" 
                                   value="{{ $search }}" placeholder="Search IP, URL, User Agent...">
                        </div>
                        <div class="col-md-2">
                            <label for="per_page" class="form-label">Per Page</label>
                            <select name="per_page" class="form-select">
                                <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-search me-1"></i> Filter
                            </button>
                            <a href="{{ route('web-access-logs.index', ['date' => $date]) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i> Clear
                            </a>
                        </div>
                    </form>
                    
                    <!-- Summary -->
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Total entries: <strong>{{ number_format($totalEntries) }}</strong>
                        @if($search)
                            | Filtered results: <strong>{{ count($logs) }}</strong>
                        @endif
                    </div>
                    
                    <!-- Logs Table -->
                    @if(count($logs) > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Time</th>
                                        <th>IP Address</th>
                                        <th>User</th>
                                        <th>Method</th>
                                        <th>URL</th>
                                        <th>Status</th>
                                        <th>Size</th>
                                        <th>Response Time</th>
                                        <th>User Agent</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($logs as $log)
                                        @if($log)
                                        <tr>
                                            <td class="text-nowrap">
                                                {{ \Carbon\Carbon::parse($log['timestamp'])->format('H:i:s') }}
                                            </td>
                                            <td class="text-nowrap">
                                                <code>{{ $log['ip'] }}</code>
                                            </td>
                                            <td>
                                                <span class="badge {{ $log['user'] == 'Guest' ? 'bg-secondary' : 'bg-primary' }}">
                                                    {{ $log['user'] }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $log['method'] == 'GET' ? 'success' : ($log['method'] == 'POST' ? 'warning' : 'info') }}">
                                                    {{ $log['method'] }}
                                                </span>
                                            </td>
                                            <td class="text-truncate" style="max-width: 300px;" title="{{ $log['url'] }}">
                                                {{ $log['url'] }}
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $log['status'] < 300 ? 'success' : ($log['status'] < 400 ? 'warning' : 'danger') }}">
                                                    {{ $log['status'] }}
                                                </span>
                                            </td>
                                            <td class="text-nowrap">
                                                {{ number_format($log['size']) }} B
                                            </td>
                                            <td class="text-nowrap">
                                                {{ $log['response_time'] }} ms
                                            </td>
                                            <td class="text-truncate" style="max-width: 200px;" title="{{ $log['user_agent'] }}">
                                                {{ $log['user_agent'] }}
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-outline-info" 
                                                        onclick="showLogDetails({{ json_encode($log) }})">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No log entries found</h5>
                            <p class="text-muted">
                                @if($search)
                                    No entries match your search criteria.
                                @else
                                    No log file exists for {{ $date }}.
                                @endif
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Log Details Modal -->
<div class="modal fade" id="logDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Log Entry Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="logDetailsContent"></div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Modal -->
<div class="modal fade" id="statsModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Access Statistics - {{ $date }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="statsContent">
                    <div class="text-center">
                        <i class="fas fa-spinner fa-spin fa-2x"></i>
                        <p class="mt-2">Loading statistics...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
function showLogDetails(log) {
    const content = `
        <div class="row">
            <div class="col-md-6">
                <h6>Request Information</h6>
                <table class="table table-sm">
                    <tr><td><strong>Timestamp:</strong></td><td>${log.timestamp}</td></tr>
                    <tr><td><strong>IP Address:</strong></td><td><code>${log.ip}</code></td></tr>
                    <tr><td><strong>User:</strong></td><td>${log.user}</td></tr>
                    <tr><td><strong>Method:</strong></td><td><span class="badge bg-primary">${log.method}</span></td></tr>
                    <tr><td><strong>URL:</strong></td><td>${log.url}</td></tr>
                    <tr><td><strong>Status:</strong></td><td><span class="badge bg-${log.status < 300 ? 'success' : (log.status < 400 ? 'warning' : 'danger')}">${log.status}</span></td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6>Response Information</h6>
                <table class="table table-sm">
                    <tr><td><strong>Response Size:</strong></td><td>${log.size.toLocaleString()} bytes</td></tr>
                    <tr><td><strong>Response Time:</strong></td><td>${log.response_time} ms</td></tr>
                    <tr><td><strong>Referer:</strong></td><td>${log.referer || 'Direct'}</td></tr>
                    <tr><td><strong>Session ID:</strong></td><td><code>${log.session}</code></td></tr>
                </table>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-12">
                <h6>User Agent</h6>
                <code class="d-block p-2 bg-light rounded">${log.user_agent}</code>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-12">
                <h6>Raw Log Entry</h6>
                <pre class="bg-dark text-light p-3 rounded"><code>${log.raw}</code></pre>
            </div>
        </div>
    `;
    
    document.getElementById('logDetailsContent').innerHTML = content;
    new bootstrap.Modal(document.getElementById('logDetailsModal')).show();
}

function loadStats() {
    const modal = new bootstrap.Modal(document.getElementById('statsModal'));
    modal.show();
    
    fetch(`{{ route('web-access-logs.stats') }}?date={{ $date }}`)
        .then(response => response.json())
        .then(data => {
            const content = `
                <div class="row">
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h3 class="text-primary">${data.total_requests.toLocaleString()}</h3>
                                <p class="card-text">Total Requests</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h3 class="text-info">${data.unique_ips.toLocaleString()}</h3>
                                <p class="card-text">Unique IPs</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h3 class="text-success">${(data.status_codes['200'] || 0).toLocaleString()}</h3>
                                <p class="card-text">Success (200)</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h3 class="text-danger">${Object.entries(data.status_codes).filter(([code]) => code >= 400).reduce((sum, [, count]) => sum + count, 0).toLocaleString()}</h3>
                                <p class="card-text">Errors (4xx/5xx)</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-6">
                        <h6>Top IP Addresses</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead><tr><th>IP Address</th><th>Requests</th></tr></thead>
                                <tbody>
                                    ${Object.entries(data.top_ips).map(([ip, count]) => 
                                        `<tr><td><code>${ip}</code></td><td>${count}</td></tr>`
                                    ).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6>Status Code Distribution</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead><tr><th>Status Code</th><th>Count</th></tr></thead>
                                <tbody>
                                    ${Object.entries(data.status_codes).map(([code, count]) => 
                                        `<tr><td><span class="badge bg-${code < 300 ? 'success' : (code < 400 ? 'warning' : 'danger')}">${code}</span></td><td>${count}</td></tr>`
                                    ).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-12">
                        <h6>Top Requested Pages</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead><tr><th>URL</th><th>Requests</th></tr></thead>
                                <tbody>
                                    ${Object.entries(data.top_pages).slice(0, 10).map(([url, count]) => 
                                        `<tr><td class="text-truncate" style="max-width: 400px;" title="${url}">${url}</td><td>${count}</td></tr>`
                                    ).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            `;
            
            document.getElementById('statsContent').innerHTML = content;
        })
        .catch(error => {
            document.getElementById('statsContent').innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Error loading statistics: ${error.message}
                </div>
            `;
        });
}
</script>
@endsection