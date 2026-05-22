<?php $__env->startSection('title', 'Pulse · Поиск'); ?>

<?php $__env->startSection('rail'); ?>
    <section class="card rail-card">
        <div class="rail-card__header">
            <h3>Как искать</h3>
        </div>
        <div class="stack">
            <p class="section-copy">Поиск работает по имени, отделу, должности, email и тексту публикаций.</p>
            <a href="<?php echo e(route('messages')); ?>" class="button button--ghost button--small">Перейти в сообщения</a>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="content-stack content-stack--narrow">
        <section class="card">
            <form action="<?php echo e(route('search')); ?>" method="GET" class="search-form search-form--wide">
                <input type="text" name="q" value="<?php echo e($query); ?>" placeholder="Имя, отдел, должность, email или текст поста">
                <button type="submit" class="button button--primary">Найти</button>
            </form>
        </section>

        <?php if($query === ''): ?>
            <section class="empty-state">
                <h2>Введите запрос</h2>
                <p>Поиск покажет сотрудников и подходящие публикации в одной колонке.</p>
            </section>
        <?php else: ?>
            <section class="card">
                <div class="section-heading">
                    <div>
                        <p class="eyebrow">Сотрудники</p>
                        <h2>Совпадения по людям</h2>
                    </div>
                </div>

                <?php if($users->isEmpty()): ?>
                    <div class="empty-state empty-state--compact">
                        <p>Совпадений по сотрудникам нет.</p>
                    </div>
                <?php else: ?>
                    <div class="stack">
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <article class="result-card">
                                <a href="<?php echo e(route('profiles.show', $employee)); ?>" class="user-line">
                                    <img src="<?php echo e($employee->avatar_url); ?>" alt="<?php echo e($employee->full_name); ?>">
                                    <div>
                                        <strong><?php echo e($employee->full_name); ?></strong>
                                        <span><?php echo e($employee->position); ?> · <?php echo e($employee->department); ?></span>
                                    </div>
                                </a>
                                <a href="<?php echo e(route('messages', ['chat' => $employee->id])); ?>" class="button button--ghost button--small">Написать</a>
                            </article>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endif; ?>
            </section>

            <section class="card">
                <div class="section-heading">
                    <div>
                        <p class="eyebrow">Публикации</p>
                        <h2>Совпадения в ленте</h2>
                    </div>
                </div>

                <?php if($posts->isEmpty()): ?>
                    <div class="empty-state empty-state--compact">
                        <p>Совпадений по публикациям нет.</p>
                    </div>
                <?php else: ?>
                    <div class="stack">
                        <?php $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <article class="compact-post compact-post--search">
                                <div class="user-line">
                                    <img src="<?php echo e($post->user->avatar_url); ?>" alt="<?php echo e($post->user->full_name); ?>">
                                    <div>
                                        <strong><?php echo e($post->user->full_name); ?></strong>
                                        <span><?php echo e($post->created_at->format('d.m.Y H:i')); ?></span>
                                    </div>
                                </div>
                                <p><?php echo e(\Illuminate\Support\Str::limit($post->body, 220)); ?></p>
                                <a href="<?php echo e(route('dashboard')); ?>" class="button button--ghost button--small">Открыть ленту</a>
                            </article>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endif; ?>
            </section>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\OSPanel\home\socnetwork\resources\views\search\index.blade.php ENDPATH**/ ?>