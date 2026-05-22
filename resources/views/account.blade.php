@extends('layouts.master')

@section('title', 'Pulse · Аккаунт')

@section('rail')
    <section class="card rail-card">
        <div class="rail-card__header">
            <h3>Рабочий профиль</h3>
        </div>
        <div class="stack">
            <div class="mini-profile">
                <img src="{{ $user->avatar_url }}" alt="{{ $user->full_name }}">
                <div>
                    <strong>{{ $user->full_name }}</strong>
                    <span>{{ $user->position }}</span>
                </div>
            </div>
            <span class="pill">{{ $user->department }}</span>
            <p class="section-copy">Здесь можно обновить публичные данные профиля, пароль и тему интерфейса.</p>
        </div>
    </section>
@endsection

@section('content')
    <div class="content-stack content-stack--narrow">
        <section class="card profile-summary">
            <div class="profile-summary__toolbar">
                <div class="user-line">
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->full_name }}" class="profile-summary__avatar">
                    <div>
                        <strong>{{ $user->full_name }}</strong>
                        <span>{{ $user->position }} · {{ $user->department }}</span>
                    </div>
                </div>

                <button type="button"
                    class="button button--ghost theme-toggle"
                    data-theme-toggle
                    aria-label="Включить светлую тему"
                    title="Включить светлую тему">
                    <span class="sr-only" data-theme-toggle-label>Включить светлую тему</span>
                    <span class="theme-toggle__thumb" aria-hidden="true"></span>
                    <span class="ui-icon ui-icon--moon theme-toggle__icon theme-toggle__icon--moon" aria-hidden="true"></span>
                    <span class="ui-icon ui-icon--sun theme-toggle__icon theme-toggle__icon--sun" aria-hidden="true"></span>
                </button>
            </div>
        </section>

        <section class="card">
            <div class="section-heading">
                <div>
                    <p class="eyebrow">Профиль</p>
                    <h2>Личные данные</h2>
                </div>
            </div>

            <form action="{{ route('account.profile') }}" method="POST" enctype="multipart/form-data" class="stack-form">
                @csrf
                @method('PUT')
                <div class="form-grid">
                    <label>
                        <span>Имя</span>
                        <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}" required>
                    </label>
                    <label>
                        <span>Фамилия</span>
                        <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}">
                    </label>
                    <label>
                        <span>Телефон</span>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}">
                    </label>
                    <label>
                        <span>Email</span>
                        <input type="email" value="{{ $user->email }}" disabled>
                    </label>
                    <label>
                        <span>Должность</span>
                        <input type="text" value="{{ $user->position }}" disabled>
                    </label>
                    <label>
                        <span>Отдел</span>
                        <input type="text" value="{{ $user->department }}" disabled>
                    </label>
                </div>
                <label class="file-picker">
                    <span>Новый аватар</span>
                    <input type="file" name="avatar">
                </label>
                <button type="submit" class="button button--primary">Сохранить профиль</button>
            </form>
        </section>

        <section class="card">
            <div class="section-heading">
                <div>
                    <p class="eyebrow">Безопасность</p>
                    <h2>Смена пароля</h2>
                </div>
            </div>

            <form action="{{ route('account.password') }}" method="POST" class="stack-form">
                @csrf
                @method('PUT')
                <div class="form-grid">
                    <label>
                        <span>Текущий пароль</span>
                        <input type="password" name="current_password" required>
                    </label>
                    <label>
                        <span>Новый пароль</span>
                        <input type="password" name="password" required>
                    </label>
                    <label>
                        <span>Подтверждение пароля</span>
                        <input type="password" name="password_confirmation" required>
                    </label>
                </div>
                <button type="submit" class="button button--primary">Обновить пароль</button>
            </form>
        </section>
    </div>
@endsection
