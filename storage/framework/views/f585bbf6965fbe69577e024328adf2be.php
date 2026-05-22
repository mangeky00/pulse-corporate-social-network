<?php $__env->startSection('title', 'Pulse · Лента'); ?>

<?php $__env->startSection('rail'); ?>
    <section class="card rail-card">
        <div class="rail-card__header">
            <h3>Что можно сделать</h3>
        </div>
        <div class="rail-links">
            <a href="<?php echo e(route('messages')); ?>">Открыть сообщения</a>
            <a href="<?php echo e(route('search')); ?>">Найти коллегу</a>
            <a href="<?php echo e(route('account')); ?>">Обновить профиль</a>
        </div>
    </section>

    <?php if(auth()->user()->is_admin): ?>
        <section class="card rail-card">
            <div class="rail-card__header">
                <h3>Для администратора</h3>
            </div>
            <div class="stack">
                <p class="section-copy">Публикуйте важные обновления в общий поток и держите команду в одном контексте.</p>
                <a href="#new-post-panel" class="button button--primary button--small">Новый пост</a>
            </div>
        </section>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="content-stack content-stack--narrow">
        <?php if(auth()->user()->is_admin): ?>
            <section class="card composer-card" id="new-post-panel">
                <div class="section-heading">
                    <div>
                        <p class="eyebrow">Публикация</p>
                        <h2>Новый пост</h2>
                    </div>
                </div>

                <form action="<?php echo e(route('posts.store')); ?>" method="POST" enctype="multipart/form-data" class="stack-form">
                    <?php echo csrf_field(); ?>
                    <textarea name="body" rows="4" placeholder="Что важно сообщить команде?" required><?php echo e(old('body')); ?></textarea>
                    <div class="form-row">
                        <label class="file-picker file-picker--icon" aria-label="Вложить файлы" title="Вложить файлы">
                            <span class="sr-only">Вложить файлы</span>
                            <span class="ui-icon ui-icon--clip" aria-hidden="true"></span>
                            <span>Вложить файлы</span>
                            <small>Фото, документы и материалы к посту</small>
                            <input type="file" name="attachments[]" multiple>
                        </label>
                        <button type="submit" class="button button--primary">Опубликовать</button>
                    </div>
                </form>
            </section>
        <?php endif; ?>

        <section class="feed">
            <?php $__empty_1 = true; $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php ($isLiked = $userLikes->has($post->id)); ?>

                <article class="post-card" data-post-card data-post-id="<?php echo e($post->id); ?>">
                    <header class="post-card__header">
                        <a href="<?php echo e(route('profiles.show', $post->user)); ?>" class="user-line">
                            <img src="<?php echo e($post->user->avatar_url); ?>" alt="<?php echo e($post->user->full_name); ?>">
                            <div>
                                <strong><?php echo e($post->user->full_name); ?></strong>
                                <span><?php echo e($post->user->position); ?> · <?php echo e($post->created_at->format('d.m.Y H:i')); ?></span>
                            </div>
                        </a>
                    </header>

                    <div class="post-card__body">
                        <?php echo nl2br(e($post->body)); ?>

                    </div>

                    <?php if($post->attachments->isNotEmpty()): ?>
                        <div class="attachment-grid">
                            <?php $__currentLoopData = $post->attachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attachment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if($attachment->file_type === 'image'): ?>
                                    <a href="<?php echo e($attachment->url); ?>" target="_blank" rel="noopener" class="attachment-card attachment-card--image">
                                        <img src="<?php echo e($attachment->url); ?>" alt="<?php echo e($attachment->file_name); ?>">
                                    </a>
                                <?php else: ?>
                                    <a href="<?php echo e($attachment->url); ?>" class="attachment-card">
                                        <span>Документ</span>
                                        <strong><?php echo e($attachment->file_name); ?></strong>
                                    </a>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php endif; ?>

                    <footer class="post-card__footer">
                        <div class="action-row">
                            <button type="button"
                                class="button button--ghost button--small button--icon <?php echo e($isLiked ? 'is-liked' : ''); ?>"
                                data-like-button
                                data-like-url="<?php echo e(route('posts.like', $post)); ?>"
                                aria-label="Нравится"
                                title="Нравится">
                                <span class="ui-icon ui-icon--heart" aria-hidden="true"></span>
                                Нравится <span data-like-count><?php echo e($post->likes_count); ?></span>
                            </button>
                            <button type="button" class="button button--ghost button--small button--icon" data-comment-toggle aria-label="Комментарии" title="Комментарии">
                                <span class="ui-icon ui-icon--comment" aria-hidden="true"></span>
                                Комментарии <span data-comment-count><?php echo e($post->comments_count); ?></span>
                            </button>
                        </div>

                        <?php if(auth()->user()->is_admin): ?>
                            <details class="inline-editor">
                                <summary>Редактирование поста</summary>
                                <form action="<?php echo e(route('posts.update', $post)); ?>" method="POST" class="stack-form">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('PUT'); ?>
                                    <textarea name="body" rows="4" required><?php echo e($post->body); ?></textarea>
                                    <div class="form-row">
                                        <button type="submit" class="button button--primary button--small">Сохранить</button>
                                    </div>
                                </form>
                                <form action="<?php echo e(route('posts.destroy', $post)); ?>" method="POST">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="button button--danger button--small">Удалить пост</button>
                                </form>
                            </details>
                        <?php endif; ?>
                    </footer>

                    <section class="comments-shell is-hidden" data-comments-shell>
                        <div class="comment-list">
                            <?php $__currentLoopData = $post->comments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $comment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <article class="comment-card" data-comment-id="<?php echo e($comment->id); ?>">
                                    <img src="<?php echo e($comment->user->avatar_url); ?>" alt="<?php echo e($comment->user->full_name); ?>">
                                    <div>
                                        <div class="comment-card__meta">
                                            <strong><?php echo e($comment->user->full_name); ?></strong>
                                            <span><?php echo e($comment->created_at->format('d.m.Y H:i')); ?></span>
                                        </div>
                                        <p><?php echo e($comment->body); ?></p>
                                    </div>
                                    <?php if(auth()->user()->is_admin || auth()->id() === $comment->user_id): ?>
                                        <button type="button"
                                            class="comment-delete"
                                            data-delete-comment
                                            data-delete-url="<?php echo e(route('posts.comments.destroy', $comment)); ?>">
                                            Удалить
                                        </button>
                                    <?php endif; ?>
                                </article>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>

                        <form class="comment-form" data-comment-form data-comment-url="<?php echo e(route('posts.comments.store', $post)); ?>">
                            <input type="text" name="body" placeholder="Добавить комментарий" required>
                            <button type="submit" class="button button--primary button--small">Ответить</button>
                        </form>
                    </section>
                </article>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <section class="empty-state">
                    <h2>Пока нет публикаций</h2>
                    <p>Первый апдейт появится здесь сразу после публикации.</p>
                </section>
            <?php endif; ?>
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\OSPanel\home\socnetwork\resources\views\dashboard.blade.php ENDPATH**/ ?>