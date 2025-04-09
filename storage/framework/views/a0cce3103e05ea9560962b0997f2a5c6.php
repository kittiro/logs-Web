<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Log Web App</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">

    <div class="container mx-auto bg-white p-6 rounded shadow">
        <h2 class="text-2xl font-bold mb-4">Logmanagement Systems</h2>
        
        <div class="flex justify-end mb-4">
            <a href="<?php echo e(route('logs.checksum')); ?>" class="bg-blue-500 text-white px-4 py-2 rounded">
                Download file checksum
            </a>
        </div>

        <table class="min-w-full bg-white border border-gray-200 shadow">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border p-2">File</th>
                    <th class="border p-2">SHA256</th>
                    <th class="border p-2">Size</th>
                    <th class="border p-2">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $logFiles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="border">
                        <td class="p-2"><?php echo e($file['name']); ?></td>
                        <td class="p-2 font-mono text-xs truncate max-w-xs"><?php echo e($file['sha256']); ?></td>
                        <td class="p-2"><?php echo e($file['size']); ?></td>
                        <td class="p-2">
                            <a href="<?php echo e($file['url']); ?>" class="bg-green-500 text-white px-3 py-1 rounded">
                                Download
                            </a>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>

    <form action="<?php echo e(route('logout')); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <button type="submit">Logout</button>
    </form>

</body>
</html>
<?php /**PATH /Users/sz-macbook/Log-Web-App/resources/views/logs/index.blade.php ENDPATH**/ ?>