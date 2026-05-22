@extends('layouts.master')

@section('title', $profile->full_name . ' · Pulse')

@section('rail')
    <section class="card rail-card">
        <div class="rail-card__header">
            <h3>Контакты</h3>
        </div>
        <dl class="details-list details-list--compact">
            <div>
                <dt>Email</dt>
                <dd>{{ $profile->email }}</dd>
            </div>
            <div>
                <dt>Телефон</dt>
                <dd>{{ $profile->phone ?: 'Не указан' }}</dd>
            </div>
            <div>
                <dt>Отдел</dt>
                <dd>{{ $profile->department }}</dd>
            </div>
        </dl>

        @if (auth()->id() !== $profile->id)
            <a href="{{ route('messages', ['chat' => $profile->id]) }}" class="button button--primary button--small">Написать</a>
        @endif
    </section>
@endsection

@section('content')
    @php
        $fromChatId = request()->integer('from_chat');
    @endphp

    <div class="content-stack content-stack--narrow">
        <section class="card profile-banner">
            <div class="profile-banner__body">
                <img src="{{ $profile->avatar_url }}" alt="{{ $profile->full_name }}" class="profile-banner__avatar">
                <div class="profile-banner__content">
                    <div class="profile-banner__header">
                        <div class="profile-banner__info">
                            <p class="eyebrow">Профиль</p>
                            <h1>{{ $profile->full_name }}</h1>
                            <p class="profile-banner__lead">{{ $profile->position }} · {{ $profile->department }}</p>
                        </div>
                        <div class="profile-banner__actions">
                            @if ($fromChatId)
                                <a href="{{ route('messages', ['chat' => $fromChatId]) }}" class="button button--ghost button--small">Назад в чат</a>
                            @endif
                            @if (auth()->id() !== $profile->id)
                                <a href="{{ route('messages', ['chat' => $profile->id]) }}" class="button button--primary button--small">Написать</a>
                            @endif
                        </div>
                    </div>

                    <div class="profile-badges">
                        <span class="pill">{{ $profile->department }}</span>
                        <span class="pill">{{ $profile->phone ?: 'Телефон не указан' }}</span>
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
                                <dd>{{ $profile->email }}</dd>
                            </div>
                            <div>
                                <dt>Телефон</dt>
                                <dd>{{ $profile->phone ?: 'Не указан' }}</dd>
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
                                <dd>{{ $profile->position }}</dd>
                            </div>
                            <div>
                                <dt>Отдел</dt>
                                <dd>{{ $profile->department }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
