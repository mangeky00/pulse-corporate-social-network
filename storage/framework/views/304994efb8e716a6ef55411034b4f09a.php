<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', config('app.name', 'Pulse')); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        (() => {
            try {
                const theme = localStorage.getItem('pulse-theme');
                if (theme === 'light' || theme === 'dark') {
                    document.documentElement.dataset.theme = theme;
                }
            } catch (error) {
                console.error(error);
            }
        })();
    </script>
    <link rel="stylesheet" href="<?php echo e(asset('src/css/main.css')); ?>">
</head>

<?php
    $isAuthenticated = auth()->check();
    $isAdminPage = $isAuthenticated && request()->routeIs('admin.*');
    $isMessagesPage = $isAuthenticated && request()->routeIs('messages*');
?>

<body class="<?php echo e($isAuthenticated ? 'layout-app' : 'layout-guest'); ?><?php echo e($isMessagesPage ? ' page-messages' : ''); ?><?php echo e($isAdminPage ? ' page-admin' : ''); ?>">
    <?php if(auth()->guard()->check()): ?>
        <div class="app-shell <?php echo e($isAdminPage ? 'app-shell--with-rail' : 'app-shell--no-rail'); ?><?php echo e($isMessagesPage ? ' app-shell--messages' : ''); ?>">
            <aside class="app-sidebar">
                <div class="app-sidebar__sticky">
                    <a class="brand brand--sidebar" href="<?php echo e(route('dashboard')); ?>">
                        <span class="brand__mark" aria-hidden="true"></span>
                        <span class="brand__label"><?php echo e(config('app.name', 'Pulse')); ?></span>
                    </a>

                    <nav class="app-nav">
                        <a href="<?php echo e(route('dashboard')); ?>" class="app-nav__item <?php echo e(request()->routeIs('dashboard') ? 'is-active' : ''); ?>">
                            <span>Лента</span>
                        </a>
                        <a href="<?php echo e(route('messages')); ?>" class="app-nav__item <?php echo e(request()->routeIs('messages*') ? 'is-active' : ''); ?>">
                            <span>Сообщения</span>
                            <span class="nav-badge is-hidden" data-unread-badge></span>
                        </a>
                        <a href="<?php echo e(route('search')); ?>" class="app-nav__item <?php echo e(request()->routeIs('search') ? 'is-active' : ''); ?>">
                            <span>Поиск</span>
                        </a>
                        <a href="<?php echo e(route('account')); ?>" class="app-nav__item <?php echo e(request()->routeIs('account*') ? 'is-active' : ''); ?>">
                            <span>Аккаунт</span>
                        </a>
                        <?php if(auth()->user()->is_admin): ?>
                            <a href="<?php echo e(route('admin.dashboard')); ?>" class="app-nav__item <?php echo e(request()->routeIs('admin.*') ? 'is-active' : ''); ?>">
                                <span>Админка</span>
                            </a>
                        <?php endif; ?>
                    </nav>

                    <?php if(auth()->user()->is_admin): ?>
                        <a href="<?php echo e(request()->routeIs('dashboard') ? '#new-post-panel' : route('dashboard')); ?>" class="app-sidebar__cta">
                            Опубликовать
                        </a>
                    <?php endif; ?>

                    <div class="sidebar-profile">
                        <a href="<?php echo e(route('profiles.show', auth()->user())); ?>" class="user-line">
                            <img src="<?php echo e(auth()->user()->avatar_url); ?>" alt="<?php echo e(auth()->user()->full_name); ?>">
                            <div>
                                <strong><?php echo e(auth()->user()->full_name); ?></strong>
                                <span><?php echo e(auth()->user()->position); ?></span>
                            </div>
                        </a>
                        <div class="sidebar-profile__meta">
                            <span class="pill"><?php echo e(auth()->user()->department); ?></span>
                            <form action="<?php echo e(route('logout')); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="button button--ghost button--small">Выйти</button>
                            </form>
                        </div>
                    </div>
                </div>
            </aside>

            <div class="app-main">
                <main class="content-column">
                    <?php echo $__env->make('partials.flash', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <?php echo $__env->yieldContent('content'); ?>
                </main>
            </div>

            <?php if($isAdminPage): ?>
            <aside class="app-rail">
                <div class="app-rail__sticky">
                    <?php if (! empty(trim($__env->yieldContent('rail')))): ?>
                        <?php echo $__env->yieldContent('rail'); ?>
                    <?php else: ?>
                        <section class="card rail-card">
                            <div class="rail-card__header">
                                <h3>Быстрый поиск</h3>
                            </div>
                            <form action="<?php echo e(route('search')); ?>" method="GET" class="stack-form">
                                <input type="text" name="q" placeholder="Имя, отдел или пост">
                                <button type="submit" class="button button--primary button--small">Открыть поиск</button>
                            </form>
                        </section>

                        <section class="card rail-card">
                            <div class="rail-card__header">
                                <h3>Навигация</h3>
                            </div>
                            <div class="rail-links">
                                <a href="<?php echo e(route('dashboard')); ?>">Лента команды</a>
                                <a href="<?php echo e(route('messages')); ?>">Личные и групповые чаты</a>
                                <a href="<?php echo e(route('account')); ?>">Настройки профиля</a>
                            </div>
                        </section>
                    <?php endif; ?>
                </div>
            </aside>
            <?php endif; ?>
        </div>

        <nav class="mobile-nav">
            <a href="<?php echo e(route('dashboard')); ?>" class="<?php echo e(request()->routeIs('dashboard') ? 'is-active' : ''); ?>">Лента</a>
            <a href="<?php echo e(route('messages')); ?>" class="<?php echo e(request()->routeIs('messages*') ? 'is-active' : ''); ?>">Чаты</a>
            <a href="<?php echo e(route('search')); ?>" class="<?php echo e(request()->routeIs('search') ? 'is-active' : ''); ?>">Поиск</a>
            <a href="<?php echo e(route('account')); ?>" class="<?php echo e(request()->routeIs('account*') ? 'is-active' : ''); ?>">Аккаунт</a>
        </nav>
    <?php else: ?>
        <div class="guest-shell">
            <main class="guest-main">
                <?php echo $__env->make('partials.flash', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php echo $__env->yieldContent('content'); ?>
            </main>
        </div>
    <?php endif; ?>

    <script>
        window.App = {
            csrfToken: <?php echo json_encode(csrf_token(), 15, 512) ?>,
            userId: <?php echo json_encode(auth()->id(), 15, 512) ?>,
            routes: {
                conversations: <?php echo json_encode(route('messages.conversations'), 15, 512) ?>,
            },
            reverb: {
                key: <?php echo json_encode(env('REVERB_APP_KEY'), 15, 512) ?>,
                host: <?php echo json_encode(env('REVERB_HOST'), 15, 512) ?>,
                port: <?php echo json_encode((int) env('REVERB_PORT', 8080), 512) ?>,
                scheme: <?php echo json_encode(env('REVERB_SCHEME', 'http'), 512) ?>,
            },
        };
    </script>
    <script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
    <script src="<?php echo e(asset('src/js/app.js')); ?>"></script>
</body>

</html>
<?php /**PATH C:\OSPanel\home\socnetwork\resources\views/layouts/master.blade.php ENDPATH**/ ?>