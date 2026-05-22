<?php $__env->startSection('title', 'Pulse · Админка'); ?>

<?php $__env->startSection('rail'); ?>
    <section class="card rail-card">
        <div class="rail-card__header">
            <h3>Разделы</h3>
        </div>
        <div class="rail-links">
            <a href="<?php echo e(route('admin.users.index')); ?>">Пользователи</a>
            <a href="<?php echo e(route('admin.posts.index')); ?>">Публикации</a>
            <a href="<?php echo e(route('dashboard')); ?>">Открыть общую ленту</a>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="content-stack content-stack--narrow">
        <section class="metric-grid">
            <article class="metric-card">
                <span>Сотрудники</span>
                <strong><?php echo e($stats['users']); ?></strong>
            </article>
            <article class="metric-card">
                <span>Посты</span>
                <strong><?php echo e($stats['posts']); ?></strong>
            </article>
            <article class="metric-card">
                <span>Комментарии</span>
                <strong><?php echo e($stats['comments']); ?></strong>
            </article>
            <article class="metric-card">
                <span>Сообщения</span>
                <strong><?php echo e($stats['messages']); ?></strong>
            </article>
            <article class="metric-card">
                <span>Группы</span>
                <strong><?php echo e($stats['groups']); ?></strong>
            </article>
        </section>

        <section class="card">
            <div class="section-heading">
                <div>
                    <p class="eyebrow">Действия</p>
                    <h2>Быстрый доступ</h2>
                </div>
            </div>

            <div class="button-group">
                <a href="<?php echo e(route('admin.users.index')); ?>" class="button button--ghost">Управление пользователями</a>
                <a href="<?php echo e(route('admin.posts.index')); ?>" class="button button--ghost">Модерация публикаций</a>
            </div>
        </section>

        <section class="card">
            <div class="section-heading">
                <div>
                    <p class="eyebrow">Активность</p>
                    <h2>Последние события</h2>
                </div>
            </div>

            <div class="stack">
                <?php $__empty_1 = true; $__currentLoopData = $recentActivity; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <article class="activity-row">
                        <img src="<?php echo e($activity['user']->avatar_url); ?>" alt="<?php echo e($activity['user']->full_name); ?>">
                        <div>
                            <strong><?php echo e($activity['user']->full_name); ?></strong>
                            <p><?php echo e($activity['text']); ?></p>
                        </div>
                        <span><?php echo e($activity['created_at']->format('d.m H:i')); ?></span>
                    </article>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="empty-state empty-state--compact">
                        <p>Активность пока не зафиксирована.</p>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\OSPanel\home\socnetwork\resources\views\admin\dashboard.blade.php ENDPATH**/ ?>