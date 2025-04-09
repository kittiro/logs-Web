<form action="<?php echo e(route('logout')); ?>" method="POST" style="display: inline;">
    <?php echo csrf_field(); ?>
    <button type="submit" class="btn btn-danger">Logout</button>
</form>
<?php /**PATH /Users/sz-macbook/Log-Web-App/resources/views/layouts/app.blade.php ENDPATH**/ ?>