

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-6">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-2xl font-bold mb-0">Log Analytics Dashboard</h2>
        <a href="<?php echo e(route('logs.index')); ?>" class="btn btn-primary">
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
                        <?php echo e($logData['purchaseStatus']['totalEvents']); ?> events
                        <span class="float-end"><?php echo e($logData['purchaseStatus']['timeRange']); ?></span>
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
                        <?php echo e($logData['purchaseSuccessRate']['totalEvents']); ?> events
                        <span class="float-end"><?php echo e($logData['purchaseSuccessRate']['timeRange']); ?></span>
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
                        <?php echo e($logData['bestSellingItems']['totalEvents']); ?> events
                        <span class="float-end"><?php echo e($logData['bestSellingItems']['timeRange']); ?></span>
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
                        <?php echo e($logData['topErrors']['totalEvents']); ?> events
                        <span class="float-end"><?php echo e($logData['topErrors']['timeRange']); ?></span>
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
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Purchase Status Chart
        new Chart(document.getElementById('purchaseStatusChart'), {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($logData['purchaseStatus']['labels'], 15, 512) ?>,
                datasets: <?php echo json_encode($logData['purchaseStatus']['datasets'], 15, 512) ?>
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
                        <?php echo e($logData['purchaseSuccessRate']['succeeded']); ?>, 
                        <?php echo e($logData['purchaseSuccessRate']['failed']); ?>

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
                labels: <?php echo json_encode($logData['bestSellingItems']['labels'], 15, 512) ?>,
                datasets: [{
                    label: 'Sales',
                    data: <?php echo json_encode($logData['bestSellingItems']['data'], 15, 512) ?>,
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
                labels: <?php echo json_encode($logData['topErrors']['labels'], 15, 512) ?>,
                datasets: [{
                    label: 'Errors',
                    data: <?php echo json_encode($logData['topErrors']['data'], 15, 512) ?>,
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
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/resources/views/dashboard.blade.php ENDPATH**/ ?>