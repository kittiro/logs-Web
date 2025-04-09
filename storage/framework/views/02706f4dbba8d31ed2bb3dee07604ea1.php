<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function toggleTheme() {
            const htmlElement = document.documentElement;
            const isDark = htmlElement.classList.toggle("dark");
            localStorage.setItem("theme", isDark ? "dark" : "light");

            // อัปเดตไอคอนให้ตรงกับธีม
            document.querySelector('.light-icon').classList.toggle('hidden', isDark);
            document.querySelector('.dark-icon').classList.toggle('hidden', !isDark);
        }

        document.addEventListener("DOMContentLoaded", () => {
            const isDark = localStorage.getItem("theme") === "dark";
            document.documentElement.classList.toggle("dark", isDark);

            // อัปเดตไอคอนตอนโหลดหน้า
            document.querySelector('.light-icon').classList.toggle('hidden', isDark);
            document.querySelector('.dark-icon').classList.toggle('hidden', !isDark);
        });
    </script>
</head>
<body class="bg-gradient-to-br from-blue-100 to-cyan-200 dark:from-gray-900 dark:to-gray-800 h-screen flex justify-center items-center">
    <div class="bg-white/40 dark:bg-gray-900/50 backdrop-blur-lg shadow-lg rounded-2xl p-8 w-96 text-gray-800 dark:text-white border border-gray-300 dark:border-gray-600">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-3xl font-semibold text-blue-700 dark:text-blue-300">Login</h2>
            <button onclick="toggleTheme()" class="w-10 h-10 flex items-center justify-center bg-gray-200 dark:bg-gray-700 rounded-full shadow-md transition duration-300">
                <span class="light-icon">🌞</span>
                <span class="dark-icon hidden">🌙</span>
            </button>
        </div>
        <form action="<?php echo e(route('login.submit')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <div class="mb-4">
                <label for="username" class="block text-sm font-medium text-blue-800 dark:text-blue-300">Username</label>
                <input type="text" name="username" id="username" required 
                       class="w-full p-3 rounded-lg bg-white/50 dark:bg-gray-700 border border-blue-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-400 dark:focus:ring-blue-500 text-gray-800 dark:text-white placeholder-gray-500 dark:placeholder-gray-400">
                <?php $__errorArgs = ['username'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="text-red-500 text-sm mt-1"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-blue-800 dark:text-blue-300">Password</label>
                <input type="password" name="password" id="password" required 
                       class="w-full p-3 rounded-lg bg-white/50 dark:bg-gray-700 border border-blue-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-400 dark:focus:ring-blue-500 text-gray-800 dark:text-white placeholder-gray-500 dark:placeholder-gray-400">
                <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="text-red-500 text-sm mt-1"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <button type="submit" class="w-full bg-blue-400 hover:bg-blue-500 dark:bg-blue-600 dark:hover:bg-blue-700 text-white font-semibold py-2 rounded-lg shadow-lg transition duration-300">
                Login
            </button>
        </form>
        <?php if($errors->any()): ?>
            <div class="mt-4 text-red-500 text-sm">
                <ul>
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
<?php /**PATH /Users/sz-macbookpro/Downloads/Log-Web-App-Edited/resources/views/auth/login.blade.php ENDPATH**/ ?>