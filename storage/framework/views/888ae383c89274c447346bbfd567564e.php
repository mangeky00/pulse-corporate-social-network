<?php $__env->startSection('title', 'Pulse · Вход'); ?>

<?php $__env->startSection('content'); ?>
    <section class="auth-shell">
        <a class="brand brand--auth" href="<?php echo e(route('home')); ?>" aria-label="<?php echo e(config('app.name', 'Pulse')); ?>">
            <span class="brand__mark" aria-hidden="true"></span>
            <span class="brand__label"><?php echo e(config('app.name', 'Pulse')); ?></span>
        </a>

        <article class="card auth-form <?php echo e($canSetupAdmin ? 'auth-form--setup' : ''); ?>">
            <h1 class="sr-only">Вход в систему</h1>
            <?php if($canSetupAdmin): ?>
                <form action="<?php echo e(route('setup-admin')); ?>" method="POST" class="stack-form">
                    <?php echo csrf_field(); ?>
                    <div class="form-grid">
                        <label>
                            <span>Имя</span>
                            <input type="text" name="first_name" autocomplete="given-name" required>
                        </label>
                        <label>
                            <span>Фамилия</span>
                            <input type="text" name="last_name" autocomplete="family-name">
                        </label>
                    </div>
                    <label>
                        <span>Email</span>
                        <input type="email" name="email" autocomplete="email" required>
                    </label>
                    <div class="form-grid">
                        <label>
                            <span>Пароль</span>
                            <input type="password" name="password" autocomplete="new-password" required>
                        </label>
                        <label>
                            <span>Подтверждение</span>
                            <input type="password" name="password_confirmation" autocomplete="new-password" required>
                        </label>
                    </div>
                    <button type="submit" class="button button--primary">Создать администратора</button>
                </form>
            <?php else: ?>
                <form action="<?php echo e(route('login')); ?>" method="POST" class="stack-form">
                    <?php echo csrf_field(); ?>
                    <label>
                        <span>Email</span>
                        <input type="email" name="email" value="<?php echo e(old('email')); ?>" autocomplete="email" required>
                    </label>
                    <label>
                        <span>Пароль</span>
                        <input type="password" name="password" autocomplete="current-password" required>
                    </label>
                    <label class="checkbox">
                        <input type="checkbox" name="remember" value="1">
                        <span>Оставаться в системе</span>
                    </label>
                    <button type="submit" class="button button--primary">Войти</button>
                </form>
            <?php endif; ?>
        </article>
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\OSPanel\home\socnetwork\resources\views/auth/login.blade.php ENDPATH**/ ?>