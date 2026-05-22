@extends('layouts.master')

@section('title', 'Pulse · Админка')

@section('rail')
    <section class="card rail-card">
        <div class="rail-card__header">
            <h3>Разделы</h3>
        </div>
        <div class="rail-links">
            <a href="{{ route('admin.users.index') }}">Пользователи</a>
            <a href="{{ route('admin.posts.index') }}">Публикации</a>
            <a href="{{ route('dashboard') }}">Открыть общую ленту</a>
        </div>
    </section>
@endsection

@section('content')
    <div class="content-stack content-stack--narrow">
        <section class="metric-grid">
            <article class="metric-card">
                <span>Сотрудники</span>
                <strong>{{ $stats['users'] }}</strong>
            </article>
            <article class="metric-card">
                <span>Посты</span>
                <strong>{{ $stats['posts'] }}</strong>
            </article>
            <article class="metric-card">
                <span>Комментарии</span>
                <strong>{{ $stats['comments'] }}</strong>
            </article>
            <article class="metric-card">
                <span>Сообщения</span>
                <strong>{{ $stats['messages'] }}</strong>
            </article>
            <article class="metric-card">
                <span>Группы</span>
                <strong>{{ $stats['groups'] }}</strong>
            </article>
        </section>

        <section class="card">
            <div class="section-heading">
                <div>
                    <p class="eyebrow">Действия</p>
                    <h2>Быстрый доступ</h2>
                </div>
            </div>

            <div class="button-group">
                <a href="{{ route('admin.users.index') }}" class="button button--ghost">Управление пользователями</a>
                <a href="{{ route('admin.posts.index') }}" class="button button--ghost">Модерация публикаций</a>
            </div>
        </section>

        <section class="card">
            <div class="section-heading">
                <div>
                    <p class="eyebrow">Активность</p>
                    <h2>Последние события</h2>
                </div>
            </div>

            <div class="stack">
                @forelse ($recentActivity as $activity)
                    <article class="activity-row">
                        <img src="{{ $activity['user']->avatar_url }}" alt="{{ $activity['user']->full_name }}">
                        <div>
                            <strong>{{ $activity['user']->full_name }}</strong>
                            <p>{{ $activity['text'] }}</p>
                        </div>
                        <span>{{ $activity['created_at']->format('d.m H:i') }}</span>
                    </article>
                @empty
                    <div class="empty-state empty-state--compact">
                        <p>Активность пока не зафиксирована.</p>
                    </div>
                @endforelse
            </div>
        </section>
    </div>
@endsection
