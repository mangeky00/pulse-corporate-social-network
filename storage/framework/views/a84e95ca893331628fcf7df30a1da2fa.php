<header>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?php echo e(route('home')); ?>"><?php echo e(config('app.name', 'Pulse')); ?></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <?php if(auth()->guard()->check()): ?>
                        <li class="nav-item">
                            <a class="btn btn-light" aria-current="page" href="<?php echo e(route('dashboard')); ?>">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-light" href="<?php echo e(route('account')); ?>">Account</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
            <ul class="nav navbar-nav navbar-right">
                <?php if(auth()->guard()->check()): ?>
                    <li><a class="btn btn-outline-danger" href="<?php echo e(route('logout')); ?>">Logout</a></li>
                <?php else: ?>
                    <li><a class="btn btn-outline-primary" href="<?php echo e(route('home')); ?>">Sign in</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
</header>
<?php /**PATH C:\OSPanel\home\socnetwork\resources\views\includs\header.blade.php ENDPATH**/ ?>