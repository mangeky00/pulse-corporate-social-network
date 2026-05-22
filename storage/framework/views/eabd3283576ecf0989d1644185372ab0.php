<?php $__env->startSection('title', 'Pulse · Аккаунт'); ?>

<?php $__env->startSection('rail'); ?>
    <section class="card rail-card">
        <div class="rail-card__header">
            <h3>Рабочий профиль</h3>
        </div>
        <div class="stack">
            <div class="mini-profile">
                <img src="<?php echo e($user->avatar_url); ?>" alt="<?php echo e($user->full_name); ?>">
                <div>
                    <strong><?php echo e($user->full_name); ?></strong>
                    <span><?php echo e($user->position); ?></span>
                </div>
            </div>
            <span class="pill"><?php echo e($user->department); ?></span>
            <p class="section-copy">Здесь можно обновить публичные данные профиля, пароль и тему интерфейса.</p>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="content-stack content-stack--narrow">
        <section class="card profile-summary">
            <div class="user-line">
                <img src="<?php echo e($user->avatar_url); ?>" alt="<?php echo e($user->full_name); ?>" class="profile-summary__avatar">
                <div>
                    <strong><?php echo e($user->full_name); ?></strong>
                    <span><?php echo e($user->position); ?> · <?php echo e($user->department); ?></span>
                </div>
            </div>
        </section>

        <section class="card">
            <div class="section-heading">
                <div>
                    <p class="eyebrow">Внешний вид</p>
                    <h2>Тема интерфейса</h2>
                </div>
            </div>

            <div class="appearance-panel">
                <div>
                    <strong class="appearance-panel__title">Текущая тема: <span data-theme-current>Темная</span></strong>
                    <p class="section-copy">Переключение сохраняется в этом браузере и применяется ко всем страницам.</p>
                </div>
                <button type="button" class="button button--ghost" data-theme-toggle>
                    <span data-theme-toggle-label>Включить светлую тему</span>
                </button>
            </div>
        </section>

        <section class="card">
            <div class="section-heading">
                <div>
                    <p class="eyebrow">Профиль</p>
                    <h2>Личные данные</h2>
                </div>
            </div>

            <form action="<?php echo e(route('account.profile')); ?>" method="POST" enctype="multipart/form-data" class="stack-form">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="form-grid">
                    <label>
                        <span>Имя</span>
                        <input type="text" name="first_name" value="<?php echo e(old('first_name', $user->first_name)); ?>" required>
                    </label>
                    <label>
                        <span>Фамилия</span>
                        <input type="text" name="last_name" value="<?php echo e(old('last_name', $user->last_name)); ?>">
                    </label>
                    <label>
                        <span>Телефон</span>
                        <input type="text" name="phone" value="<?php echo e(old('phone', $user->phone)); ?>">
                    </label>
                    <label>
                        <span>Email</span>
                        <input type="email" value="<?php echo e($user->email); ?>" disabled>
                    </label>
                    <label>
                        <span>Должность</span>
                        <input type="text" value="<?php echo e($user->position); ?>" disabled>
                    </label>
                    <label>
                        <span>Отдел</span>
                        <input type="text" value="<?php echo e($user->department); ?>" disabled>
                    </label>
                </div>
                <label class="file-picker">
                    <span>Новый аватар</span>
                    <input type="file" name="avatar">
                </label>
                <button type="submit" class="button button--primary">Сохранить профиль</button>
            </form>
        </section>

        <section class="card">
            <div class="section-heading">
                <div>
                    <p class="eyebrow">Безопасность</p>
                    <h2>Смена пароля</h2>
                </div>
            </div>

            <form action="<?php echo e(route('account.password')); ?>" method="POST" class="stack-form">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="form-grid">
                    <label>
                        <span>Текущий пароль</span>
                        <input type="password" name="current_password" required>
                    </label>
                    <label>
                        <span>Новый пароль</span>
                        <input type="password" name="password" required>
                    </label>
                    <label>
                        <span>Подтверждение пароля</span>
                        <input type="password" name="password_confirmation" required>
                    </label>
                </div>
                <button type="submit" class="button button--primary">Обновить пароль</button>
            </form>
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\OSPanel\home\socnetwork\resources\views\account.blade.php ENDPATH**/ ?>