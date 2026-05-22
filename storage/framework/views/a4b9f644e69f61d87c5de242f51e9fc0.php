<?php $__env->startSection('title', 'Pulse · Публикации'); ?>

<?php $__env->startSection('rail'); ?>
    <section class="card rail-card">
        <div class="rail-card__header">
            <h3>Модерация</h3>
        </div>
        <p class="section-copy">Удаление доступно как по одному посту, так и пакетно через отмеченные записи.</p>
    </section>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="content-stack content-stack--wide">
        <section class="card">
            <form action="<?php echo e(route('admin.posts.bulk-destroy')); ?>" method="POST" class="stack-form">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>

                <div class="table-wrap">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Автор</th>
                                <th>Пост</th>
                                <th>Статистика</th>
                                <th>Дата</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><input type="checkbox" name="post_ids[]" value="<?php echo e($post->id); ?>"></td>
                                    <td><?php echo e($post->user->full_name); ?></td>
                                    <td><?php echo e(\Illuminate\Support\Str::limit($post->body, 120)); ?></td>
                                    <td>Нравится <?php echo e($post->likes_count); ?> · Комментарии <?php echo e($post->comments_count); ?> · Файлы <?php echo e($post->attachments->count()); ?></td>
                                    <td><?php echo e($post->created_at->format('d.m.Y H:i')); ?></td>
                                    <td>
                                        <button type="submit" form="delete-post-<?php echo e($post->id); ?>" class="button button--danger button--small">Удалить</button>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>

                <button type="submit" class="button button--danger">Удалить выбранные</button>
            </form>

            <?php $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <form id="delete-post-<?php echo e($post->id); ?>" action="<?php echo e(route('admin.posts.destroy', $post)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                </form>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\OSPanel\home\socnetwork\resources\views\admin\posts.blade.php ENDPATH**/ ?>