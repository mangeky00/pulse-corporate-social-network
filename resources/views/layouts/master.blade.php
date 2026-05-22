<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Pulse'))</title>
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
    <link rel="stylesheet" href="{{ asset('src/css/main.css') }}">
</head>

@php
    $isAuthenticated = auth()->check();
    $isAdminPage = $isAuthenticated && request()->routeIs('admin.*');
    $isMessagesPage = $isAuthenticated && request()->routeIs('messages*');
@endphp

<body class="{{ $isAuthenticated ? 'layout-app' : 'layout-guest' }}{{ $isMessagesPage ? ' page-messages' : '' }}{{ $isAdminPage ? ' page-admin' : '' }}">
    @auth
        <div class="app-shell {{ $isAdminPage ? 'app-shell--with-rail' : 'app-shell--no-rail' }}{{ $isMessagesPage ? ' app-shell--messages' : '' }}">
            <aside class="app-sidebar">
                <div class="app-sidebar__sticky">
                    <a class="brand brand--sidebar" href="{{ route('dashboard') }}">
                        <span class="brand__mark" aria-hidden="true"></span>
                        <span class="brand__label">{{ config('app.name', 'Pulse') }}</span>
                    </a>

                    <nav class="app-nav">
                        <a href="{{ route('dashboard') }}" class="app-nav__item {{ request()->routeIs('dashboard') ? 'is-active' : '' }}">
                            <span>Лента</span>
                        </a>
                        <a href="{{ route('messages') }}" class="app-nav__item {{ request()->routeIs('messages*') ? 'is-active' : '' }}">
                            <span>Сообщения</span>
                            <span class="nav-badge is-hidden" data-unread-badge></span>
                        </a>
                        <a href="{{ route('search') }}" class="app-nav__item {{ request()->routeIs('search') ? 'is-active' : '' }}">
                            <span>Поиск</span>
                        </a>
                        <a href="{{ route('account') }}" class="app-nav__item {{ request()->routeIs('account*') ? 'is-active' : '' }}">
                            <span>Аккаунт</span>
                        </a>
                        @if (auth()->user()->is_admin)
                            <a href="{{ route('admin.dashboard') }}" class="app-nav__item {{ request()->routeIs('admin.*') ? 'is-active' : '' }}">
                                <span>Админка</span>
                            </a>
                        @endif
                    </nav>

                    @if (auth()->user()->is_admin)
                        <a href="{{ request()->routeIs('dashboard') ? '#new-post-panel' : route('dashboard') }}" class="app-sidebar__cta">
                            Опубликовать
                        </a>
                    @endif

                    <div class="sidebar-profile">
                        <a href="{{ route('profiles.show', auth()->user()) }}" class="user-line">
                            <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->full_name }}">
                            <div>
                                <strong>{{ auth()->user()->full_name }}</strong>
                                <span>{{ auth()->user()->position }}</span>
                            </div>
                        </a>
                        <div class="sidebar-profile__meta">
                            <span class="pill">{{ auth()->user()->department }}</span>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="button button--ghost button--small">Выйти</button>
                            </form>
                        </div>
                    </div>
                </div>
            </aside>

            <div class="app-main">
                <main class="content-column">
                    @include('partials.flash')
                    @yield('content')
                </main>
            </div>

            @if ($isAdminPage)
            <aside class="app-rail">
                <div class="app-rail__sticky">
                    @hasSection('rail')
                        @yield('rail')
                    @else
                        <section class="card rail-card">
                            <div class="rail-card__header">
                                <h3>Быстрый поиск</h3>
                            </div>
                            <form action="{{ route('search') }}" method="GET" class="stack-form">
                                <input type="text" name="q" placeholder="Имя, отдел или пост">
                                <button type="submit" class="button button--primary button--small">Открыть поиск</button>
                            </form>
                        </section>

                        <section class="card rail-card">
                            <div class="rail-card__header">
                                <h3>Навигация</h3>
                            </div>
                            <div class="rail-links">
                                <a href="{{ route('dashboard') }}">Лента команды</a>
                                <a href="{{ route('messages') }}">Личные и групповые чаты</a>
                                <a href="{{ route('account') }}">Настройки профиля</a>
                            </div>
                        </section>
                    @endif
                </div>
            </aside>
            @endif
        </div>

        <nav class="mobile-nav">
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'is-active' : '' }}">Лента</a>
            <a href="{{ route('messages') }}" class="{{ request()->routeIs('messages*') ? 'is-active' : '' }}">Чаты</a>
            <a href="{{ route('search') }}" class="{{ request()->routeIs('search') ? 'is-active' : '' }}">Поиск</a>
            <a href="{{ route('account') }}" class="{{ request()->routeIs('account*') ? 'is-active' : '' }}">Аккаунт</a>
        </nav>
    @else
        <div class="guest-shell">
            <main class="guest-main">
                @include('partials.flash')
                @yield('content')
            </main>
        </div>
    @endauth

    <script>
        window.App = {
            csrfToken: @json(csrf_token()),
            userId: @json(auth()->id()),
            routes: {
                conversations: @json(route('messages.conversations')),
            },
            reverb: {
                key: @json(env('REVERB_APP_KEY')),
                host: @json(env('REVERB_HOST')),
                port: @json((int) env('REVERB_PORT', 8080)),
                scheme: @json(env('REVERB_SCHEME', 'http')),
            },
        };
    </script>
    <script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
    <script src="{{ asset('src/js/app.js') }}"></script>
</body>

</html>
