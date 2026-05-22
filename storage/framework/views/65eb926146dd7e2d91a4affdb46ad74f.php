<?php $__env->startSection('title', 'Pulse · Сообщения'); ?>

<?php $__env->startSection('rail'); ?>
    <?php
        $activeMembership = $activeGroup
            ? $activeGroup->memberships->firstWhere('user_id', $user->id)
            : null;
        $canManageGroup = $activeGroup && ($activeGroup->created_by === $user->id || optional($activeMembership)->role === 'owner');
    ?>

    <?php if($activeType === 'direct' && $activeDirect): ?>
        <section class="card rail-card">
            <div class="rail-card__header">
                <h3>Текущий диалог</h3>
            </div>
            <div class="mini-profile">
                <img src="<?php echo e($activeDirect->avatar_url); ?>" alt="<?php echo e($activeDirect->full_name); ?>">
                <div>
                    <strong><?php echo e($activeDirect->full_name); ?></strong>
                    <span><?php echo e($activeDirect->position); ?></span>
                </div>
            </div>
            <a href="<?php echo e(route('profiles.show', ['user' => $activeDirect, 'from_chat' => $activeDirect->id])); ?>" class="button button--ghost button--small">Открыть профиль</a>
        </section>
    <?php elseif($activeType === 'group' && $activeGroup): ?>
        <section class="card rail-card">
            <div class="rail-card__header">
                <h3><?php echo e($activeGroup->name); ?></h3>
            </div>
            <p class="section-copy">Участников: <?php echo e($activeGroup->memberships->count()); ?></p>
            <?php if($canManageGroup): ?>
                <button type="button" class="button button--ghost button--small" data-modal-open="manage-group-modal">Управление группой</button>
            <?php endif; ?>
        </section>
    <?php else: ?>
        <section class="card rail-card">
            <div class="rail-card__header">
                <h3>Сообщения</h3>
            </div>
            <p class="section-copy">Откройте личный или групповой чат, чтобы продолжить рабочий диалог.</p>
        </section>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php
        $activeMembership = $activeGroup
            ? $activeGroup->memberships->firstWhere('user_id', $user->id)
            : null;
        $canManageGroup = $activeGroup && ($activeGroup->created_by === $user->id || optional($activeMembership)->role === 'owner');
    ?>

    <div class="content-stack content-stack--wide">
        <section class="messages-layout" data-messages-page>
            <aside class="card conversations-panel">
                <div class="conversation-toolbar">
                    <p class="conversation-section__title">Диалоги</p>
                    <div class="button-group">
                        <button type="button" class="button button--ghost button--small" data-modal-open="new-chat-modal">Новый чат</button>
                        <button type="button" class="button button--primary button--small" data-modal-open="new-group-modal">Новая группа</button>
                    </div>
                </div>

                <div class="conversation-section">
                    <p class="conversation-section__title">Личные</p>
                    <div class="conversation-stack" data-conversation-list="direct">
                        <?php $__empty_1 = true; $__currentLoopData = $directConversations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $conversation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <a href="<?php echo e($conversation['href']); ?>"
                                class="conversation-card <?php echo e($activeType === 'direct' && $activeDirect?->id === $conversation['id'] ? 'is-active' : ''); ?>"
                                data-conversation-card
                                data-conversation-type="<?php echo e($conversation['type']); ?>"
                                data-conversation-id="<?php echo e($conversation['id']); ?>">
                                <img src="<?php echo e($conversation['avatar_url']); ?>" alt="<?php echo e($conversation['name']); ?>">
                                <div class="conversation-card__body">
                                    <strong><?php echo e($conversation['name']); ?></strong>
                                    <span><?php echo e($conversation['subtitle']); ?></span>
                                </div>
                                <?php if($conversation['unread_count'] > 0): ?>
                                    <span class="nav-badge"><?php echo e($conversation['unread_count']); ?></span>
                                <?php endif; ?>
                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="empty-state empty-state--compact">
                                <p>Личных диалогов пока нет.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="conversation-section">
                    <p class="conversation-section__title">Группы</p>
                    <div class="conversation-stack" data-conversation-list="group">
                        <?php $__empty_1 = true; $__currentLoopData = $groupConversations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $conversation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <a href="<?php echo e($conversation['href']); ?>"
                                class="conversation-card <?php echo e($activeType === 'group' && $activeGroup?->id === $conversation['id'] ? 'is-active' : ''); ?>"
                                data-conversation-card
                                data-conversation-type="<?php echo e($conversation['type']); ?>"
                                data-conversation-id="<?php echo e($conversation['id']); ?>">
                                <img src="<?php echo e($conversation['avatar_url']); ?>" alt="<?php echo e($conversation['name']); ?>">
                                <div class="conversation-card__body">
                                    <strong><?php echo e($conversation['name']); ?></strong>
                                    <span><?php echo e($conversation['subtitle']); ?></span>
                                </div>
                                <?php if($conversation['unread_count'] > 0): ?>
                                    <span class="nav-badge"><?php echo e($conversation['unread_count']); ?></span>
                                <?php endif; ?>
                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="empty-state empty-state--compact">
                                <p>Групповых чатов пока нет.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </aside>

            <section class="card chat-panel">
                <?php if($activeType === 'direct' && $activeDirect): ?>
                    <header class="chat-panel__header">
                        <div class="user-line">
                            <img src="<?php echo e($activeDirect->avatar_url); ?>" alt="<?php echo e($activeDirect->full_name); ?>">
                            <div>
                                <strong><?php echo e($activeDirect->full_name); ?></strong>
                                <span><?php echo e($activeDirect->position); ?></span>
                            </div>
                        </div>
                        <a href="<?php echo e(route('profiles.show', ['user' => $activeDirect, 'from_chat' => $activeDirect->id])); ?>" class="button button--ghost button--small">Профиль</a>
                    </header>
                <?php elseif($activeType === 'group' && $activeGroup): ?>
                    <header class="chat-panel__header">
                        <div class="user-line">
                            <img src="<?php echo e(asset('uploads/' . \App\Support\AvatarDefaults::GROUP)); ?>" alt="<?php echo e($activeGroup->name); ?>">
                            <div>
                                <strong><?php echo e($activeGroup->name); ?></strong>
                                <span>Групповой чат · <?php echo e($activeGroup->memberships->count()); ?> участников</span>
                            </div>
                        </div>
                        <div class="button-group">
                            <?php if($canManageGroup): ?>
                                <button type="button" class="button button--ghost button--small" data-modal-open="manage-group-modal">Управление</button>
                            <?php endif; ?>
                            <?php if($activeGroup->created_by === $user->id): ?>
                                <form action="<?php echo e(route('messages.groups.destroy', $activeGroup)); ?>" method="POST">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="button button--danger button--small">Удалить группу</button>
                                </form>
                            <?php else: ?>
                                <form action="<?php echo e(route('messages.groups.leave', $activeGroup)); ?>" method="POST">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="button button--ghost button--small">Покинуть</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </header>
                <?php else: ?>
                    <div class="chat-empty">
                        <h2>Выберите диалог</h2>
                        <p>Откройте личный или групповой чат слева, чтобы начать переписку.</p>
                    </div>
                <?php endif; ?>

                <?php if($activeType && (($activeType === 'direct' && $activeDirect) || ($activeType === 'group' && $activeGroup))): ?>
                    <div id="messages-stream"
                        class="messages-stream"
                        data-messages-stream
                        data-type="<?php echo e($activeType); ?>"
                        data-id="<?php echo e($activeType === 'direct' ? $activeDirect->id : $activeGroup->id); ?>"
                        data-refresh-url="<?php echo e($activeType === 'direct' ? route('messages.direct.data', $activeDirect) : route('messages.group.data', $activeGroup)); ?>">
                        <?php $__currentLoopData = $messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <article class="message-bubble <?php echo e($message['sender_id'] === $user->id ? 'message-bubble--own' : ''); ?>">
                                <?php if($message['sender_id'] !== $user->id): ?>
                                    <img src="<?php echo e($message['sender_avatar_url']); ?>" alt="<?php echo e($message['sender_name']); ?>">
                                <?php endif; ?>
                                <div class="message-bubble__content">
                                    <?php if($activeType === 'group' && $message['sender_id'] !== $user->id): ?>
                                        <strong><?php echo e($message['sender_name']); ?></strong>
                                    <?php endif; ?>
                                    <?php if($message['body']): ?>
                                        <p><?php echo e($message['body']); ?></p>
                                    <?php endif; ?>
                                    <?php if(! empty($message['attachments'])): ?>
                                        <div class="message-attachments">
                                            <?php $__currentLoopData = $message['attachments']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attachment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php if($attachment['file_type'] === 'image'): ?>
                                                    <a href="<?php echo e($attachment['url']); ?>" target="_blank" rel="noopener" class="message-attachment message-attachment--image">
                                                        <img src="<?php echo e($attachment['url']); ?>" alt="<?php echo e($attachment['file_name']); ?>">
                                                    </a>
                                                <?php else: ?>
                                                    <a href="<?php echo e($attachment['url']); ?>" class="message-attachment message-attachment--file"><?php echo e($attachment['file_name']); ?></a>
                                                <?php endif; ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    <?php endif; ?>
                                    <span><?php echo e($message['created_at_label']); ?></span>
                                </div>
                            </article>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>

                    <form id="message-form"
                        class="message-form"
                        data-message-form
                        data-submit-url="<?php echo e($activeType === 'direct' ? route('messages.direct.store') : route('messages.group.store')); ?>">
                        <?php if($activeType === 'direct'): ?>
                            <input type="hidden" name="receiver_id" value="<?php echo e($activeDirect->id); ?>">
                        <?php else: ?>
                            <input type="hidden" name="group_chat_id" value="<?php echo e($activeGroup->id); ?>">
                        <?php endif; ?>

                        <label class="file-picker file-picker--compact file-picker--icon" aria-label="Вложить файлы" title="Вложить файлы">
                            <span class="sr-only">Вложить файлы</span>
                            <span class="ui-icon ui-icon--clip" aria-hidden="true"></span>
                            <span>Вложения</span>
                            <small>Фото и документы</small>
                            <input type="file" name="attachments[]" multiple>
                        </label>
                        <input type="text" name="body" placeholder="Введите сообщение" autocomplete="off">
                        <button type="submit" class="button button--primary">Отправить</button>
                    </form>
                <?php endif; ?>
            </section>
        </section>
    </div>

    <div class="modal" data-modal="new-chat-modal">
        <div class="modal__dialog">
            <div class="modal__header">
                <h2>Новый чат</h2>
                <button type="button" data-modal-close>x</button>
            </div>
            <div class="modal__body stack">
                <label class="modal-search">
                    <span class="sr-only">Поиск пользователя</span>
                    <input type="search" placeholder="Найти по имени, должности или отделу" data-user-search-input>
                </label>
                <div class="modal-list" data-user-search-list>
                    <?php $__currentLoopData = $availableUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a
                            href="<?php echo e(route('messages', ['chat' => $employee->id])); ?>"
                            class="conversation-card"
                            data-user-search-item
                            data-search-text="<?php echo e($employee->full_name); ?> <?php echo e($employee->position); ?> <?php echo e($employee->department); ?>">
                            <img src="<?php echo e($employee->avatar_url); ?>" alt="<?php echo e($employee->full_name); ?>">
                            <div class="conversation-card__body">
                                <strong><?php echo e($employee->full_name); ?></strong>
                                <span><?php echo e($employee->position); ?> · <?php echo e($employee->department); ?></span>
                            </div>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <div class="empty-state empty-state--compact is-hidden" data-user-search-empty>
                    <p>Пользователи не найдены.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" data-modal="new-group-modal">
        <div class="modal__dialog">
            <div class="modal__header">
                <h2>Новая группа</h2>
                <button type="button" data-modal-close>x</button>
            </div>
            <div class="modal__body">
                <form action="<?php echo e(route('messages.groups.store')); ?>" method="POST" class="stack-form">
                    <?php echo csrf_field(); ?>
                    <label>
                        <span>Название группы</span>
                        <input type="text" name="name" required>
                    </label>
                    <div class="checkbox-list">
                        <?php $__currentLoopData = $availableUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <label class="checkbox-card">
                                <input type="checkbox" name="members[]" value="<?php echo e($employee->id); ?>">
                                <span><?php echo e($employee->full_name); ?> · <?php echo e($employee->position); ?></span>
                            </label>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <button type="submit" class="button button--primary">Создать группу</button>
                </form>
            </div>
        </div>
    </div>

    <?php if($activeGroup && $canManageGroup): ?>
        <div class="modal" data-modal="manage-group-modal">
            <div class="modal__dialog">
                <div class="modal__header">
                    <h2>Управление группой</h2>
                    <button type="button" data-modal-close>x</button>
                </div>
                <div class="modal__body stack">
                    <form action="<?php echo e(route('messages.groups.update', $activeGroup)); ?>" method="POST" class="stack-form">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>
                        <label>
                            <span>Название</span>
                            <input type="text" name="name" value="<?php echo e($activeGroup->name); ?>" required>
                        </label>
                        <button type="submit" class="button button--primary">Сохранить</button>
                    </form>

                    <div class="stack">
                        <h3>Участники</h3>
                        <?php $__currentLoopData = $activeGroup->memberships; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $membership): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <article class="member-row">
                                <div class="user-line">
                                    <img src="<?php echo e($membership->user->avatar_url); ?>" alt="<?php echo e($membership->user->full_name); ?>">
                                    <div>
                                        <strong><?php echo e($membership->user->full_name); ?></strong>
                                        <span><?php echo e($membership->role === 'owner' ? 'Owner' : $membership->user->position); ?></span>
                                    </div>
                                </div>
                                <?php if($membership->user_id !== $activeGroup->created_by): ?>
                                    <form action="<?php echo e(route('messages.groups.members.destroy', [$activeGroup, $membership->user])); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="button button--ghost button--small">Убрать</button>
                                    </form>
                                <?php endif; ?>
                            </article>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>

                    <?php if($groupCandidates->isNotEmpty()): ?>
                        <form action="<?php echo e(route('messages.groups.members.store', $activeGroup)); ?>" method="POST" class="stack-form">
                            <?php echo csrf_field(); ?>
                            <label>
                                <span>Добавить участника</span>
                                <select name="user_id">
                                    <?php $__currentLoopData = $groupCandidates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $candidate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($candidate->id); ?>"><?php echo e($candidate->full_name); ?> · <?php echo e($candidate->position); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </label>
                            <button type="submit" class="button button--primary">Добавить</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\OSPanel\home\socnetwork\resources\views\messages\index.blade.php ENDPATH**/ ?>