@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-3">
        <div class="col-md-6">
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
                <input type="text" class="form-control" id="searchInput" placeholder="Search logs...">
            </div>
        </div>
        <div class="col-md-3 ms-auto">
            <div class="d-flex justify-content-end">
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="sizeFilterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        Filter by size
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="sizeFilterDropdown">
                        <li><a class="dropdown-item" href="#" data-size="all">All sizes</a></li>
                        <li><a class="dropdown-item" href="#" data-size="small">Small (< 10KB)</a></li>
                        <li><a class="dropdown-item" href="#" data-size="medium">Medium (10KB - 100KB)</a></li>
                        <li><a class="dropdown-item" href="#" data-size="large">Large (> 100KB)</a></li>
                    </ul>
                </div>
                <a href="{{ route('logs.checksum.all') }}" class="btn btn-primary ms-2">
                    <i class="fas fa-download me-1"></i> Download file checksum
                </a>
                <a href="{{ route('dashboard') }}" class="btn btn-info ms-2">
                    <i class="fas fa-chart-pie me-1"></i> Analytics Dashboard
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover" id="logsTable">
                    <thead>
                        <tr>
                            <th scope="col">File <i class="fas fa-sort"></i></th>
                            <th scope="col">SHA256 <i class="fas fa-sort"></i></th>
                            <th scope="col">Size <i class="fas fa-sort"></i></th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logFiles as $file)
                        <tr>
                            <td class="align-middle">
                                <i class="fas fa-file-alt me-2"></i> {{ $file['name'] }}
                            </td>
                            <td class="align-middle">
                                <div class="d-flex align-items-center">
                                    {{ substr($file['sha256'], 0, 32) }}...
                                    <button class="btn btn-sm ms-2 copy-btn" data-clipboard-text="{{ $file['sha256'] }}" title="Copy to clipboard">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                            </td>
                            <td class="align-middle">{{ $file['size'] }}</td>
                            <td class="align-middle">
                                <div class="d-flex gap-2">
                                    <a href="{{ $file['download_url'] }}" class="btn btn-success">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    <button class="btn btn-primary rounded-circle preview-btn" data-path="{{ storage_path('logs/' . $file['name']) }}">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/clipboard@2.0.8/dist/clipboard.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize clipboard.js
        new ClipboardJS('.copy-btn');
        
        // Search functionality
        const searchInput = document.getElementById('searchInput');
        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('#logsTable tbody tr');
            
            tableRows.forEach(row => {
                const fileName = row.querySelector('td:first-child').textContent.toLowerCase();
                if (fileName.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
        
        // Size filter functionality
        const filterItems = document.querySelectorAll('.dropdown-item[data-size]');
        filterItems.forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                const filterValue = this.getAttribute('data-size');
                const tableRows = document.querySelectorAll('#logsTable tbody tr');
                
                document.getElementById('sizeFilterDropdown').textContent = this.textContent;
                
                tableRows.forEach(row => {
                    const sizeText = row.querySelector('td:nth-child(3)').textContent;
                    const sizeInKB = parseFloat(sizeText);
                    
                    switch(filterValue) {
                        case 'small':
                            row.style.display = (sizeInKB < 10) ? '' : 'none';
                            break;
                        case 'medium':
                            row.style.display = (sizeInKB >= 10 && sizeInKB <= 100) ? '' : 'none';
                            break;
                        case 'large':
                            row.style.display = (sizeInKB > 100) ? '' : 'none';
                            break;
                        default:
                            row.style.display = '';
                    }
                });
            });
        });
    });
</script>
@endpush

@push('styles')
<style>
    .preview-btn {
        width: 38px;
        height: 38px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
    
    .copy-btn {
        color: var(--text-color);
        background-color: transparent;
        border: none;
    }
    
    .copy-btn:hover {
        color: #0d6efd;
    }
    
    pre.file-content {
        max-height: 500px;
        overflow-y: auto;
        white-space: pre-wrap;
        word-wrap: break-word;
    }

    .btn-success {
        background-color: #28a745;
        border-color: #28a745;
    }

    .btn-success:hover {
        background-color: #218838;
        border-color: #1e7e34;
    }
    
    #logsTable th {
        padding-left: 1rem;
        font-weight: 500;
    }
    
    #logsTable td {
        padding-left: 1rem;
    }
    
    .dropdown-toggle::after {
        margin-left: 0.5em;
    }
</style>
@endpush
