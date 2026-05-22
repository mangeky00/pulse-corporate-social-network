@extends('layouts.master')

@section('title', 'Pulse · Лента')

@section('rail')
    <section class="card rail-card">
        <div class="rail-card__header">
            <h3>Что можно сделать</h3>
        </div>
        <div class="rail-links">
            <a href="{{ route('messages') }}">Открыть сообщения</a>
            <a href="{{ route('search') }}">Найти коллегу</a>
            <a href="{{ route('account') }}">Обновить профиль</a>
        </div>
    </section>

    @if (auth()->user()->is_admin)
        <section class="card rail-card">
            <div class="rail-card__header">
                <h3>Для администратора</h3>
            </div>
            <div class="stack">
                <p class="section-copy">Публикуйте важные обновления в общий поток и держите команду в одном контексте.</p>
                <a href="#new-post-panel" class="button button--primary button--small">Новый пост</a>
            </div>
        </section>
    @endif
@endsection

@section('content')
    <div class="content-stack content-stack--narrow">
        @if (auth()->user()->is_admin)
            <section class="card composer-card" id="new-post-panel">
                <div class="section-heading">
                    <div>
                        <p class="eyebrow">Публикация</p>
                        <h2>Новый пост</h2>
                    </div>
                </div>

                <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data" class="stack-form">
                    @csrf
                    <textarea name="body" rows="4" placeholder="Что важно сообщить команде?" required>{{ old('body') }}</textarea>
                    <div class="form-row">
                        <label class="file-picker file-picker--icon" aria-label="Вложить файлы" title="Вложить файлы">
                            <span class="sr-only">Вложить файлы</span>
                            <span class="ui-icon ui-icon--clip" aria-hidden="true"></span>
                            <span>Вложить файлы</span>
                            <small>Фото, документы и материалы к посту</small>
                            <input type="file" name="attachments[]" multiple>
                        </label>
                        <button type="submit" class="button button--primary">Опубликовать</button>
                    </div>
                </form>
            </section>
        @endif

        <section class="feed">
            @forelse ($posts as $post)
                @php($isLiked = $userLikes->has($post->id))

                <article class="post-card" data-post-card data-post-id="{{ $post->id }}">
                    <header class="post-card__header">
                        <a href="{{ route('profiles.show', $post->user) }}" class="user-line">
                            <img src="{{ $post->user->avatar_url }}" alt="{{ $post->user->full_name }}">
                            <div>
                                <strong>{{ $post->user->full_name }}</strong>
                                <span>{{ $post->user->position }} · {{ $post->created_at->format('d.m.Y H:i') }}</span>
                            </div>
                        </a>
                    </header>

                    <div class="post-card__body">
                        {!! nl2br(e($post->body)) !!}
                    </div>

                    @if ($post->attachments->isNotEmpty())
                        <div class="attachment-grid">
                            @foreach ($post->attachments as $attachment)
                                @if ($attachment->file_type === 'image')
                                    <a href="{{ $attachment->url }}" target="_blank" rel="noopener" class="attachment-card attachment-card--image">
                                        <img src="{{ $attachment->url }}" alt="{{ $attachment->file_name }}">
                                    </a>
                                @else
                                    <a href="{{ $attachment->url }}" class="attachment-card">
                                        <span>Документ</span>
                                        <strong>{{ $attachment->file_name }}</strong>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    @endif

                    <footer class="post-card__footer">
                        <div class="action-row">
                            <button type="button"
                                class="button button--ghost button--small button--icon {{ $isLiked ? 'is-liked' : '' }}"
                                data-like-button
                                data-like-url="{{ route('posts.like', $post) }}"
                                aria-label="Нравится"
                                title="Нравится">
                                <span class="ui-icon ui-icon--heart" aria-hidden="true"></span>
                                Нравится <span data-like-count>{{ $post->likes_count }}</span>
                            </button>
                            <button type="button" class="button button--ghost button--small button--icon" data-comment-toggle aria-label="Комментарии" title="Комментарии">
                                <span class="ui-icon ui-icon--comment" aria-hidden="true"></span>
                                Комментарии <span data-comment-count>{{ $post->comments_count }}</span>
                            </button>
                        </div>

                        @if (auth()->user()->is_admin)
                            <details class="inline-editor">
                                <summary>Редактирование поста</summary>
                                <form action="{{ route('posts.update', $post) }}" method="POST" class="stack-form">
                                    @csrf
                                    @method('PUT')
                                    <textarea name="body" rows="4" required>{{ $post->body }}</textarea>
                                    <div class="form-row">
                                        <button type="submit" class="button button--primary button--small">Сохранить</button>
                                    </div>
                                </form>
                                <form action="{{ route('posts.destroy', $post) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="button button--danger button--small">Удалить пост</button>
                                </form>
                            </details>
                        @endif
                    </footer>

                    <section class="comments-shell is-hidden" data-comments-shell>
                        <div class="comment-list">
                            @foreach ($post->comments as $comment)
                                <article class="comment-card" data-comment-id="{{ $comment->id }}">
                                    <img src="{{ $comment->user->avatar_url }}" alt="{{ $comment->user->full_name }}">
                                    <div>
                                        <div class="comment-card__meta">
                                            <strong>{{ $comment->user->full_name }}</strong>
                                            <span>{{ $comment->created_at->format('d.m.Y H:i') }}</span>
                                        </div>
                                        <p>{{ $comment->body }}</p>
                                    </div>
                                    @if (auth()->user()->is_admin || auth()->id() === $comment->user_id)
                                        <button type="button"
                                            class="comment-delete"
                                            data-delete-comment
                                            data-delete-url="{{ route('posts.comments.destroy', $comment) }}">
                                            Удалить
                                        </button>
                                    @endif
                                </article>
                            @endforeach
                        </div>

                        <form class="comment-form" data-comment-form data-comment-url="{{ route('posts.comments.store', $post) }}">
                            <input type="text" name="body" placeholder="Добавить комментарий" required>
                            <button type="submit" class="button button--primary button--small">Ответить</button>
                        </form>
                    </section>
                </article>
            @empty
                <section class="empty-state">
                    <h2>Пока нет публикаций</h2>
                    <p>Первый апдейт появится здесь сразу после публикации.</p>
                </section>
            @endforelse
        </section>
    </div>
@endsection
