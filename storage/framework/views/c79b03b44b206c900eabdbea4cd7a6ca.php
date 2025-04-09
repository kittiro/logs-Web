<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo e(config('app.name', 'RLOK')); ?></title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Dark Theme (default) */
        html[data-theme="dark"] {
            --bg-color: #0a192f;
            --text-color: #e2e8f0;
            --navbar-bg: #0a192f;
            --navbar-border: #172a46;
            --card-bg: #0f2847;
            --table-row-bg: #0a192f;
            --table-row-hover: #172a46;
            --table-border: #172a46;
            --input-bg: #1e293b;
            --input-border: #334155;
            --input-focus-border: #4b5563;
            --sidebar-bg: #0a192f;
            --sidebar-hover: #172a46;
            --sidebar-text: #e2e8f0;
        }
        
        /* Light Theme */
        html[data-theme="light"] {
            --bg-color: #ffffff;
            --text-color: #333333;
            --navbar-bg: #f8f9fa;
            --navbar-border: #dee2e6;
            --card-bg: #ffffff;
            --table-row-bg: #ffffff;
            --table-row-hover: #f8f9fa;
            --table-border: #dee2e6;
            --input-bg: #ffffff;
            --input-border: #ced4da;
            --input-focus-border: #86b7fe;
            --sidebar-bg: #f8f9fa;
            --sidebar-hover: #e9ecef;
            --sidebar-text: #333333;
        }
        
        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            transition: all 0.3s ease;
        }
        
        .navbar {
            background-color: var(--navbar-bg) !important;
            border-bottom: 1px solid var(--navbar-border);
        }
        
        .card, .bg-white {
            background-color: var(--card-bg) !important;
            color: var(--text-color);
            border: none;
        }
        
        .table {
            color: var(--text-color);
        }
        
        .table thead th {
            border-bottom: 1px solid var(--table-border);
        }
        
        .table tbody tr {
            background-color: var(--table-row-bg);
            border-bottom: 1px solid var(--table-border);
        }
        
        .table tbody tr:hover {
            background-color: var(--table-row-hover);
        }
        
        .form-control, .form-select {
            background-color: var(--input-bg);
            border-color: var(--input-border);
            color: var(--text-color);
        }
        
        .form-control:focus, .form-select:focus {
            background-color: var(--input-bg);
            border-color: var(--input-focus-border);
            color: var(--text-color);
            box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.25);
        }
        
        .file-preview-modal {
            background-color: rgba(10, 25, 47, 0.9) !important;
        }
        
        .file-preview-modal .card {
            background-color: var(--card-bg) !important;
        }
        
        .file-preview-modal pre.file-content {
            background-color: var(--input-bg);
            color: var(--text-color);
        }
        
        .theme-toggle {
            cursor: pointer;
        }
        
        /* Sidebar styling */
        .offcanvas {
            background-color: var(--sidebar-bg);
            color: var(--sidebar-text);
            width: 260px;
        }
        
        .offcanvas-header {
            border-bottom: 1px solid var(--table-border);
        }
        
        .offcanvas-title {
            font-weight: 500;
        }
        
        .btn-close.text-reset {
            filter: var(--bs-theme) === 'dark' ? invert(1) : none;
        }
        
        .sidebar-menu .nav-link {
            color: var(--sidebar-text);
            padding: 0.75rem 1rem;
            border-radius: 4px;
            transition: background-color 0.2s ease;
        }
        
        .sidebar-menu .nav-link:hover {
            background-color: var(--sidebar-hover);
        }
        
        .sidebar-menu .nav-link i {
            width: 20px;
            text-align: center;
        }
        
        /* Fix for dark theme close button */
        html[data-theme="dark"] .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
        }
        
        /* Hamburger button styling */
        .navbar-toggler {
            padding: 0.25rem 0.5rem;
            font-size: 1.25rem;
            background-color: transparent;
            border: none;
            box-shadow: none !important;
            color: var(--text-color);
        }
    </style>
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>
    <div id="app">
        <!-- Sidebar for mobile -->
        <div class="offcanvas offcanvas-start" tabindex="-1" id="sidebarOffcanvas" aria-labelledby="sidebarOffcanvasLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="sidebarOffcanvasLabel"></h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <div class="sidebar-menu">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo e(route('logs.index')); ?>">
                                <i class="fas fa-file me-2"></i> File
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo e(route('logs.index')); ?>">
                                <i class="fas fa-save me-2"></i> Save
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo e(route('logs.index')); ?>">
                                <i class="fas fa-history me-2"></i> History
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo e(route('logs.index')); ?>">
                                <i class="fas fa-filter me-2"></i> Filters
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo e(route('dashboard')); ?>">
                                <i class="fas fa-chart-pie me-2"></i> Dashboard
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <nav class="navbar navbar-expand-md shadow-sm">
            <div class="container">
                <!-- Hamburger menu toggle button -->
                <button class="navbar-toggler me-2 border-0 p-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas" aria-controls="sidebarOffcanvas">
                    <i class="fas fa-bars"></i>
                </button>
                
                <a class="navbar-brand" href="<?php echo e(url('/')); ?>">
                    RLOK
                </a>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Theme Toggle -->
                        <li class="nav-item">
                            <div class="nav-link theme-toggle" id="themeToggle">
                                <i class="fas fa-moon"></i>
                            </div>
                        </li>
                        
                        <!-- Authentication Links -->
                        <?php if(auth()->guard()->guest()): ?>
                            <?php if(Route::has('login')): ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="<?php echo e(route('login')); ?>"><?php echo e(__('Login')); ?></a>
                                </li>
                            <?php endif; ?>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="btn btn-danger" href="<?php echo e(route('logout')); ?>"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt mr-1"></i> <?php echo e(__('Log Out')); ?>

                                </a>
                                <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" class="d-none">
                                    <?php echo csrf_field(); ?>
                                </form>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            <?php echo $__env->yieldContent('content'); ?>
        </main>
    </div>

    <!-- File Preview Component -->
    <?php echo $__env->make('components.file-preview', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Theme Toggle Functionality
            const themeToggle = document.getElementById('themeToggle');
            const htmlElement = document.documentElement;
            const icon = themeToggle.querySelector('i');
            
            themeToggle.addEventListener('click', function() {
                if (htmlElement.getAttribute('data-theme') === 'dark') {
                    htmlElement.setAttribute('data-theme', 'light');
                    icon.classList.remove('fa-moon');
                    icon.classList.add('fa-sun');
                } else {
                    htmlElement.setAttribute('data-theme', 'dark');
                    icon.classList.remove('fa-sun');
                    icon.classList.add('fa-moon');
                }
            });
        });
    </script>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH /var/www/resources/views/layouts/app.blade.php ENDPATH**/ ?>