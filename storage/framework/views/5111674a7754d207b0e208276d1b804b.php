<?php if($errors->any()): ?>
    <section class="flash flash--error">
        <ul>
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </section>
<?php endif; ?>

<?php if(session('message')): ?>
    <section class="flash flash--success">
        <?php echo e(session('message')); ?>

    </section>
<?php endif; ?>
<?php /**PATH C:\OSPanel\home\socnetwork\resources\views/partials/flash.blade.php ENDPATH**/ ?>