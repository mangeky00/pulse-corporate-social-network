<?php $__env->startSection('title', 'Pulse · Пользователи'); ?>

<?php $__env->startSection('rail'); ?>
    <section class="card rail-card">
        <div class="rail-card__header">
            <h3>Подсказка</h3>
        </div>
        <p class="section-copy">Создавайте сотрудников слева и редактируйте существующие карточки ниже по странице.</p>
    </section>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="content-stack content-stack--narrow">
        <section class="card">
            <div class="section-heading">
                <div>
                    <p class="eyebrow">Новый сотрудник</p>
                    <h2>Добавить пользователя</h2>
                </div>
            </div>

            <form action="<?php echo e(route('admin.users.store')); ?>" method="POST" class="stack-form">
                <?php echo csrf_field(); ?>
                <div class="form-grid">
                    <label><span>Имя</span><input type="text" name="first_name" required></label>
                    <label><span>Фамилия</span><input type="text" name="last_name"></label>
                    <label><span>Email</span><input type="email" name="email" required></label>
                    <label><span>Телефон</span><input type="text" name="phone"></label>
                    <label><span>Должность</span><input type="text" name="position" required></label>
                    <label><span>Отдел</span><input type="text" name="department" required></label>
                    <label>
                        <span>Роль</span>
                        <select name="role">
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                    </label>
                    <label><span>Пароль</span><input type="text" name="password" placeholder="password123 по умолчанию"></label>
                </div>
                <button type="submit" class="button button--primary">Создать</button>
            </form>
        </section>

        <section class="card">
            <div class="section-heading">
                <div>
                    <p class="eyebrow">Список</p>
                    <h2>Сотрудники</h2>
                </div>
            </div>

            <div class="stack">
                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <details class="details-card">
                        <summary>
                            <div class="user-line">
                                <img src="<?php echo e($employee->avatar_url); ?>" alt="<?php echo e($employee->full_name); ?>">
                                <div>
                                    <strong><?php echo e($employee->full_name); ?></strong>
                                    <span><?php echo e($employee->email); ?> · <?php echo e($employee->position); ?></span>
                                </div>
                            </div>
                            <span class="pill"><?php echo e(strtoupper($employee->role)); ?></span>
                        </summary>

                        <div class="details-card__content">
                            <form action="<?php echo e(route('admin.users.update', $employee)); ?>" method="POST" class="stack-form">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('PUT'); ?>
                                <div class="form-grid">
                                    <label><span>Имя</span><input type="text" name="first_name" value="<?php echo e($employee->first_name); ?>" required></label>
                                    <label><span>Фамилия</span><input type="text" name="last_name" value="<?php echo e($employee->last_name); ?>"></label>
                                    <label><span>Email</span><input type="email" name="email" value="<?php echo e($employee->email); ?>" required></label>
                                    <label><span>Телефон</span><input type="text" name="phone" value="<?php echo e($employee->phone); ?>"></label>
                                    <label><span>Должность</span><input type="text" name="position" value="<?php echo e($employee->position); ?>" required></label>
                                    <label><span>Отдел</span><input type="text" name="department" value="<?php echo e($employee->department); ?>" required></label>
                                    <label>
                                        <span>Роль</span>
                                        <select name="role">
                                            <option value="user" <?php if($employee->role === 'user'): echo 'selected'; endif; ?>>User</option>
                                            <option value="admin" <?php if($employee->role === 'admin'): echo 'selected'; endif; ?>>Admin</option>
                                        </select>
                                    </label>
                                </div>
                                <div class="button-group">
                                    <button type="submit" class="button button--primary button--small">Сохранить</button>
                                </div>
                            </form>

                            <div class="button-group">
                                <form action="<?php echo e(route('admin.users.password.reset', $employee)); ?>" method="POST">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('PUT'); ?>
                                    <button type="submit" class="button button--ghost button--small">Сбросить пароль</button>
                                </form>
                                <form action="<?php echo e(route('admin.users.destroy', $employee)); ?>" method="POST">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="button button--danger button--small">Удалить</button>
                                </form>
                            </div>
                        </div>
                    </details>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\OSPanel\home\socnetwork\resources\views\admin\users.blade.php ENDPATH**/ ?>