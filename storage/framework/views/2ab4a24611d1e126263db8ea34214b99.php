<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Navbar -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 py-3">
            <div class="flex justify-between items-center">
                <div class="flex items-center">
                    <button id="sidebarToggle" class="mr-4 text-gray-600 hover:text-gray-900">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <h1 class="text-xl font-bold text-gray-800">Log Management Systems</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <!-- Dark Mode Toggle -->
                    <button id="darkModeToggle" class="p-2 rounded-lg hover:bg-gray-100">
                        <i class="fas fa-moon"></i>
                    </button>

                    <!-- Log Out Button -->
                    <form method="POST" action="<?php echo e(route('logout')); ?>">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 flex items-center">
                            <i class="fas fa-sign-out-alt mr-2"></i> Log Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div id="sidebar" class="fixed left-0 top-0 h-full w-64 bg-white shadow-lg transform -translate-x-full transition-transform duration-300 ease-in-out z-50 pt-20">
        <div class="p-4">
            <div class="space-y-4">
                <a href="#" class="flex items-center space-x-2 p-2 rounded-lg hover:bg-gray-100">
                    <i class="fas fa-file-alt"></i>
                    <span>Log Files</span>
                </a>
                <a href="#" class="flex items-center space-x-2 p-2 rounded-lg hover:bg-gray-100">
                    <i class="fas fa-history"></i>
                    <span>History</span>
                </a>
                <a href="#" class="flex items-center space-x-2 p-2 rounded-lg hover:bg-gray-100">
                    <i class="fas fa-filter"></i>
                    <span>Filters</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-6">
        <!-- Search and Actions Bar -->
        <div class="bg-white rounded-lg shadow-md p-4 mb-6">
            <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                <div class="relative w-full md:w-96">
                    <input type="text" id="searchInput" placeholder="Search logs..." class="w-full pl-10 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
                <div class="flex space-x-4">
                    <select name="size_filter" class="border rounded-lg px-3 py-2">
                        <option value="">Filter by size</option>
                        <option value="small" <?php echo e(request('size_filter') == 'small' ? 'selected' : ''); ?>>Small (<1MB)</option>
                        <option value="medium" <?php echo e(request('size_filter') == 'medium' ? 'selected' : ''); ?>>Medium (1-10MB)</option>
                        <option value="large" <?php echo e(request('size_filter') == 'large' ? 'selected' : ''); ?>>Large (>10MB)</option>
                    </select>
                    <a href="#" onclick="downloadChecksum()" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 flex items-center">
                        <i class="fas fa-download mr-2"></i> Download file checksum
                    </a>
                </div>
            </div>
        </div>

        <!-- Log Files Table -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 text-sm leading-normal">
                        <th class="py-3 px-6 text-left cursor-pointer hover:bg-gray-100" onclick="sortTable(0)">
                            File <i class="fas fa-sort ml-1"></i>
                        </th>
                        <th class="py-3 px-6 text-left">SHA256</th>
                        <th class="py-3 px-6 text-left cursor-pointer hover:bg-gray-100" onclick="sortTable(2)">
                            Size <i class="fas fa-sort ml-1"></i>
                        </th>
                        <th class="py-3 px-6 text-left">Action</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm">
                    <?php $__currentLoopData = $logFiles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="border-b border-gray-200 hover:bg-gray-50 transition duration-150">
                            <td class="py-3 px-6">
                                <div class="flex items-center">
                                    <i class="fas fa-file-alt mr-2 text-gray-400"></i>
                                    <?php echo e($file['name']); ?>

                                </div>
                            </td>
                            <td class="py-3 px-6 font-mono text-xs">
                                <div class="flex items-center">
                                    <span class="truncate max-w-xs"><?php echo e($file['sha256']); ?></span>
                                    <button onclick="copyToClipboard('<?php echo e($file['sha256']); ?>')" class="ml-2 text-gray-400 hover:text-gray-600">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                            </td>
                            <td class="py-3 px-6"><?php echo e($file['size']); ?></td>
                            <td class="py-3 px-6">
                                <div class="flex items-center space-x-2">
                                    <a href="<?php echo e($file['url']); ?>" class="bg-green-500 text-white px-3 py-1 rounded-lg hover:bg-green-600 flex items-center">
                                        <i class="fas fa-download mr-1"></i> Download
                                    </a>
                                    <button onclick="previewFile('<?php echo e($file['name']); ?>')" class="bg-blue-500 text-white px-3 py-1 rounded-lg hover:bg-blue-600">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Preview Modal -->
    <div id="previewModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg w-3/4 h-3/4 p-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold">File Preview</h3>
                <button onclick="closePreviewModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <pre id="previewContent" class="bg-gray-100 p-4 rounded-lg h-5/6 overflow-auto"></pre>
        </div>
    </div>

    <script>
        // Sidebar Toggle
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('-translate-x-full');
        });

        // Dark Mode Toggle
        const darkModeToggle = document.getElementById('darkModeToggle');
        darkModeToggle.addEventListener('click', function() {
            document.documentElement.classList.toggle('dark');
            const icon = darkModeToggle.querySelector('i');
            icon.classList.toggle('fa-moon');
            icon.classList.toggle('fa-sun');
        });

        // Search Functionality
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });

        // Copy to Clipboard
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                alert('SHA256 copied to clipboard!');
            });
        }

        // Sort Table
        function sortTable(n) {
            const table = document.querySelector('table');
            const rows = Array.from(table.querySelectorAll('tbody tr'));
            const direction = table.getAttribute('data-direction') === 'asc' ? -1 : 1;
            
            rows.sort((a, b) => {
                const aText = a.children[n].textContent.trim();
                const bText = b.children[n].textContent.trim();
                return direction * (aText > bText ? 1 : -1);
            });
            
            rows.forEach(row => table.querySelector('tbody').appendChild(row));
            table.setAttribute('data-direction', direction === 1 ? 'asc' : 'desc');
        }

        // Preview Modal
        function previewFile(filename) {
            document.getElementById('previewModal').style.display = 'flex';
        }

        function closePreviewModal() {
            document.getElementById('previewModal').style.display = 'none';
        }

        function downloadChecksum() {
    // Collect all SHA256 checksums from the table
    const rows = document.querySelectorAll('tbody tr');
    let checksumContent = '';

    rows.forEach(row => {
        const filename = row.querySelector('td:first-child').textContent.trim();
        const checksum = row.querySelector('td:nth-child(2) span').textContent.trim();
        checksumContent += `${checksum}  ${filename}\n`;
    });

    // Create a Blob with the checksum content
    const blob = new Blob([checksumContent], { type: 'text/plain' });
    
    // Create a download link
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = 'checksums.txt';
    
    // Append to body, click, and remove
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
    </script>

    
</body>
</html>


<?php /**PATH /Users/sz-macbookpro/Downloads/Log-Web-App-Edited/resources/views/logs/index.blade.php ENDPATH**/ ?>