<?php $__env->startSection('title', $profile->full_name . ' · Pulse'); ?>

<?php $__env->startSection('rail'); ?>
    <section class="card rail-card">
        <div class="rail-card__header">
            <h3>Контакты</h3>
        </div>
        <dl class="details-list details-list--compact">
            <div>
                <dt>Email</dt>
                <dd><?php echo e($profile->email); ?></dd>
            </div>
            <div>
                <dt>Телефон</dt>
                <dd><?php echo e($profile->phone ?: 'Не указан'); ?></dd>
            </div>
            <div>
                <dt>Отдел</dt>
                <dd><?php echo e($profile->department); ?></dd>
            </div>
        </dl>

        <?php if(auth()->id() !== $profile->id): ?>
            <a href="<?php echo e(route('messages', ['chat' => $profile->id])); ?>" class="button button--primary button--small">Написать</a>
        <?php endif; ?>
    </section>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php
        $fromChatId = request()->integer('from_chat');
    ?>

    <div class="content-stack content-stack--narrow">
        <section class="card profile-banner">
            <div class="profile-banner__cover"></div>
            <div class="profile-banner__body">
                <img src="<?php echo e($profile->avatar_url); ?>" alt="<?php echo e($profile->full_name); ?>" class="profile-banner__avatar">
                <div class="profile-banner__content">
                    <div class="profile-banner__header">
                        <div class="profile-banner__info">
                            <p class="eyebrow">Профиль</p>
                            <h1><?php echo e($profile->full_name); ?></h1>
                            <p class="profile-banner__lead"><?php echo e($profile->position); ?> · <?php echo e($profile->department); ?></p>
                        </div>
                        <div class="profile-banner__actions">
                            <?php if($fromChatId): ?>
                                <a href="<?php echo e(route('messages', ['chat' => $fromChatId])); ?>" class="button button--ghost button--small">Назад в чат</a>
                            <?php endif; ?>
                            <?php if(auth()->id() !== $profile->id): ?>
                                <a href="<?php echo e(route('messages', ['chat' => $profile->id])); ?>" class="button button--primary button--small">Написать</a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="profile-badges">
                        <span class="pill"><?php echo e($profile->department); ?></span>
                        <span class="pill"><?php echo e($profile->phone ?: 'Телефон не указан'); ?></span>
                    </div>
                </div>
            </div>
        </section>

        <section class="card">
            <div class="section-heading">
                <div>
                    <p class="eyebrow">Информация</p>
                    <h2>Данные сотрудника</h2>
                </div>
            </div>

            <div class="profile-info-grid">
                <div class="details-card">
                    <div class="details-card__content">
                        <h3>Контакты</h3>
                        <dl class="details-list details-list--compact">
                            <div>
                                <dt>Email</dt>
                                <dd><?php echo e($profile->email); ?></dd>
                            </div>
                            <div>
                                <dt>Телефон</dt>
                                <dd><?php echo e($profile->phone ?: 'Не указан'); ?></dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <div class="details-card">
                    <div class="details-card__content">
                        <h3>Рабочая информация</h3>
                        <dl class="details-list details-list--compact">
                            <div>
                                <dt>Должность</dt>
                                <dd><?php echo e($profile->position); ?></dd>
                            </div>
                            <div>
                                <dt>Отдел</dt>
                                <dd><?php echo e($profile->department); ?></dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\OSPanel\home\socnetwork\resources\views\profile\show.blade.php ENDPATH**/ ?>