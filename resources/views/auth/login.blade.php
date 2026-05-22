@extends('layouts.master')

@section('title', 'Pulse · Вход')

@section('content')
    <section class="auth-shell">
        <a class="brand brand--auth" href="{{ route('home') }}" aria-label="{{ config('app.name', 'Pulse') }}">
            <span class="brand__mark" aria-hidden="true"></span>
            <span class="brand__label">{{ config('app.name', 'Pulse') }}</span>
        </a>

        <article class="card auth-form {{ $canSetupAdmin ? 'auth-form--setup' : '' }}">
            <h1 class="sr-only">Вход в систему</h1>
            @if ($canSetupAdmin)
                <form action="{{ route('setup-admin') }}" method="POST" class="stack-form">
                    @csrf
                    <div class="form-grid">
                        <label>
                            <span>Имя</span>
                            <input type="text" name="first_name" autocomplete="given-name" required>
                        </label>
                        <label>
                            <span>Фамилия</span>
                            <input type="text" name="last_name" autocomplete="family-name">
                        </label>
                    </div>
                    <label>
                        <span>Email</span>
                        <input type="email" name="email" autocomplete="email" required>
                    </label>
                    <div class="form-grid">
                        <label>
                            <span>Пароль</span>
                            <input type="password" name="password" autocomplete="new-password" required>
                        </label>
                        <label>
                            <span>Подтверждение</span>
                            <input type="password" name="password_confirmation" autocomplete="new-password" required>
                        </label>
                    </div>
                    <button type="submit" class="button button--primary">Создать администратора</button>
                </form>
            @else
                <form action="{{ route('login') }}" method="POST" class="stack-form">
                    @csrf
                    <label>
                        <span>Email</span>
                        <input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required>
                    </label>
                    <label>
                        <span>Пароль</span>
                        <input type="password" name="password" autocomplete="current-password" required>
                    </label>
                    <label class="checkbox">
                        <input type="checkbox" name="remember" value="1">
                        <span>Оставаться в системе</span>
                    </label>
                    <button type="submit" class="button button--primary">Войти</button>
                </form>
            @endif
        </article>
    </section>
@endsection
