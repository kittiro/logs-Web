@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-2xl font-bold mb-0">Log Analytics Dashboard</h2>
        <a href="{{ route('logs.index') }}" class="btn btn-primary">
            <i class="fas fa-arrow-left me-1"></i> Return to Logs
        </a>
    </div>

    <div class="row mb-4">
        <!-- Login Activity (formerly Purchase Status) -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Login Activity</h5>
                    <i class="fas fa-info-circle text-muted"></i>
                </div>
                <div class="card-body">
                    <div class="text-muted small mb-2">
                        {{ $logData['purchaseStatus']['totalEvents'] }} events
                        <span class="float-end">{{ $logData['purchaseStatus']['timeRange'] }}</span>
                    </div>
                    <canvas id="purchaseStatusChart" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Purchase Success Rate -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Purchase Success Rate</h5>
                    <i class="fas fa-info-circle text-muted"></i>
                </div>
                <div class="card-body">
                    <div class="text-muted small mb-2">
                        {{ $logData['purchaseSuccessRate']['totalEvents'] }} events
                        <span class="float-end">{{ $logData['purchaseSuccessRate']['timeRange'] }}</span>
                    </div>
                    <canvas id="purchaseSuccessRateChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <!-- Best Selling Items -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Best Selling Items IDs</h5>
                    <i class="fas fa-info-circle text-muted"></i>
                </div>
                <div class="card-body">
                    <div class="text-muted small mb-2">
                        {{ $logData['bestSellingItems']['totalEvents'] }} events
                        <span class="float-end">{{ $logData['bestSellingItems']['timeRange'] }}</span>
                    </div>
                    <canvas id="bestSellingItemsChart" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Top Errors -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Top Errors</h5>
                    <i class="fas fa-info-circle text-muted"></i>
                </div>
                <div class="card-body">
                    <div class="text-muted small mb-2">
                        {{ $logData['topErrors']['totalEvents'] }} events
                        <span class="float-end">{{ $logData['topErrors']['timeRange'] }}</span>
                    </div>
                    <canvas id="topErrorsChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-12 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Log Activity (Hourly)</h5>
                </div>
                <div class="card-body">
                    <canvas id="logChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Purchase Status Chart
        new Chart(document.getElementById('purchaseStatusChart'), {
            type: 'bar',
            data: {
                labels: @json($logData['purchaseStatus']['labels']),
                datasets: @json($logData['purchaseStatus']['datasets'])
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                    }
                }
            }
        });

        // Purchase Success Rate Chart
        new Chart(document.getElementById('purchaseSuccessRateChart'), {
            type: 'doughnut',
            data: {
                labels: ['Purchase succeeded', 'Purchase failed'],
                datasets: [{
                    data: [
                        {{ $logData['purchaseSuccessRate']['succeeded'] }}, 
                        {{ $logData['purchaseSuccessRate']['failed'] }}
                    ],
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(75, 192, 192, 0.7)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right',
                    }
                }
            }
        });

        // Best Selling Items Chart
        new Chart(document.getElementById('bestSellingItemsChart'), {
            type: 'bar',
            data: {
                labels: @json($logData['bestSellingItems']['labels']),
                datasets: [{
                    label: 'Sales',
                    data: @json($logData['bestSellingItems']['data']),
                    backgroundColor: [
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(153, 102, 255, 0.7)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                scales: {
                    x: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // Top Errors Chart
        new Chart(document.getElementById('topErrorsChart'), {
            type: 'bar',
            data: {
                labels: @json($logData['topErrors']['labels']),
                datasets: [{
                    label: 'Errors',
                    data: @json($logData['topErrors']['data']),
                    backgroundColor: [
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(153, 102, 255, 0.7)',
                        'rgba(255, 159, 64, 0.7)',
                        'rgba(75, 192, 192, 0.7)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                scales: {
                    x: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // Log Activity Chart
        const placeholderLabels = ['00:00', '01:00', '02:00', '03:00', '04:00', '05:00', '06:00', '07:00', '08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00', '21:00', '22:00', '23:00'];
        const placeholderData = Array.from({length: 24}, () => Math.floor(Math.random() * 50)); // Random data for now
        const logCtx = document.getElementById('logChart')?.getContext('2d');
        if (logCtx) {
            const logChart = new Chart(logCtx, {
                type: 'line', // Can be 'bar', 'line', etc.
                data: {
                    labels: placeholderLabels, // Replace with actual labels from controller
                    datasets: [{
                        label: 'Log Entries per Hour',
                        data: placeholderData, // Replace with actual data from controller
                        borderColor: 'rgb(255, 99, 132)', // Example color
                        backgroundColor: 'rgba(255, 99, 132, 0.2)', // Example fill color
                        tension: 0.1,
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    responsive: true,
                    maintainAspectRatio: false // Ensure this is false for fixed height
                }
            });

            // TODO: Fetch actual data via AJAX and update:
            // fetch('/log-activity-data') // Your new route
            //   .then(response => response.json())
            //   .then(data => {
            //      logChart.data.labels = data.labels;
            //      logChart.data.datasets[0].data = data.data;
            //      logChart.update();
            //   });
        }
    });
</script>
@endpush