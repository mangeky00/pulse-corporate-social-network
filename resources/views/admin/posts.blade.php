@extends('layouts.master')

@section('title', 'Pulse · Публикации')

@section('rail')
    <section class="card rail-card">
        <div class="rail-card__header">
            <h3>Модерация</h3>
        </div>
        <p class="section-copy">Удаление доступно как по одному посту, так и пакетно через отмеченные записи.</p>
    </section>
@endsection

@section('content')
    <div class="content-stack content-stack--wide">
        <section class="card">
            <form action="{{ route('admin.posts.bulk-destroy') }}" method="POST" class="stack-form">
                @csrf
                @method('DELETE')

                <div class="table-wrap">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Автор</th>
                                <th>Пост</th>
                                <th>Статистика</th>
                                <th>Дата</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($posts as $post)
                                <tr>
                                    <td><input type="checkbox" name="post_ids[]" value="{{ $post->id }}"></td>
                                    <td>{{ $post->user->full_name }}</td>
                                    <td>{{ \Illuminate\Support\Str::limit($post->body, 120) }}</td>
                                    <td>Нравится {{ $post->likes_count }} · Комментарии {{ $post->comments_count }} · Файлы {{ $post->attachments->count() }}</td>
                                    <td>{{ $post->created_at->format('d.m.Y H:i') }}</td>
                                    <td>
                                        <button type="submit" form="delete-post-{{ $post->id }}" class="button button--danger button--small">Удалить</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <button type="submit" class="button button--danger">Удалить выбранные</button>
            </form>

            @foreach ($posts as $post)
                <form id="delete-post-{{ $post->id }}" action="{{ route('admin.posts.destroy', $post) }}" method="POST">
                    @csrf
                    @method('DELETE')
                </form>
            @endforeach
        </section>
    </div>
@endsection
