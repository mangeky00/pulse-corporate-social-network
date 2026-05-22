@extends('layouts.master')

@section('title', 'Pulse · Поиск')

@section('rail')
    <section class="card rail-card">
        <div class="rail-card__header">
            <h3>Как искать</h3>
        </div>
        <div class="stack">
            <p class="section-copy">Поиск работает по имени, отделу, должности, email и тексту публикаций.</p>
            <a href="{{ route('messages') }}" class="button button--ghost button--small">Перейти в сообщения</a>
        </div>
    </section>
@endsection

@section('content')
    <div class="content-stack content-stack--narrow">
        <section class="card">
            <form action="{{ route('search') }}" method="GET" class="search-form search-form--wide">
                <input type="text" name="q" value="{{ $query }}" placeholder="Имя, отдел, должность, email или текст поста">
                <button type="submit" class="button button--primary">Найти</button>
            </form>
        </section>

        @if ($query === '')
            <section class="empty-state">
                <h2>Введите запрос</h2>
                <p>Поиск покажет сотрудников и подходящие публикации в одной колонке.</p>
            </section>
        @else
            <section class="card">
                <div class="section-heading">
                    <div>
                        <p class="eyebrow">Сотрудники</p>
                        <h2>Совпадения по людям</h2>
                    </div>
                </div>

                @if ($users->isEmpty())
                    <div class="empty-state empty-state--compact">
                        <p>Совпадений по сотрудникам нет.</p>
                    </div>
                @else
                    <div class="stack">
                        @foreach ($users as $employee)
                            <article class="result-card">
                                <a href="{{ route('profiles.show', $employee) }}" class="user-line">
                                    <img src="{{ $employee->avatar_url }}" alt="{{ $employee->full_name }}">
                                    <div>
                                        <strong>{{ $employee->full_name }}</strong>
                                        <span>{{ $employee->position }} · {{ $employee->department }}</span>
                                    </div>
                                </a>
                                <a href="{{ route('messages', ['chat' => $employee->id]) }}" class="button button--ghost button--small">Написать</a>
                            </article>
                        @endforeach
                    </div>
                @endif
            </section>

            <section class="card">
                <div class="section-heading">
                    <div>
                        <p class="eyebrow">Публикации</p>
                        <h2>Совпадения в ленте</h2>
                    </div>
                </div>

                @if ($posts->isEmpty())
                    <div class="empty-state empty-state--compact">
                        <p>Совпадений по публикациям нет.</p>
                    </div>
                @else
                    <div class="stack">
                        @foreach ($posts as $post)
                            <article class="compact-post compact-post--search">
                                <div class="user-line">
                                    <img src="{{ $post->user->avatar_url }}" alt="{{ $post->user->full_name }}">
                                    <div>
                                        <strong>{{ $post->user->full_name }}</strong>
                                        <span>{{ $post->created_at->format('d.m.Y H:i') }}</span>
                                    </div>
                                </div>
                                <p>{{ \Illuminate\Support\Str::limit($post->body, 220) }}</p>
                                <a href="{{ route('dashboard') }}" class="button button--ghost button--small">Открыть ленту</a>
                            </article>
                        @endforeach
                    </div>
                @endif
            </section>
        @endif
    </div>
@endsection
