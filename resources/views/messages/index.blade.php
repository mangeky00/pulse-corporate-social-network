@extends('layouts.master')

@section('title', 'Pulse · Сообщения')

@section('rail')
    @php
        $activeMembership = $activeGroup
            ? $activeGroup->memberships->firstWhere('user_id', $user->id)
            : null;
        $canManageGroup = $activeGroup && ($activeGroup->created_by === $user->id || optional($activeMembership)->role === 'owner');
    @endphp

    @if ($activeType === 'direct' && $activeDirect)
        <section class="card rail-card">
            <div class="rail-card__header">
                <h3>Текущий диалог</h3>
            </div>
            <div class="mini-profile">
                <img src="{{ $activeDirect->avatar_url }}" alt="{{ $activeDirect->full_name }}">
                <div>
                    <strong>{{ $activeDirect->full_name }}</strong>
                    <span>{{ $activeDirect->position }}</span>
                </div>
            </div>
            <a href="{{ route('profiles.show', ['user' => $activeDirect, 'from_chat' => $activeDirect->id]) }}" class="button button--ghost button--small">Открыть профиль</a>
        </section>
    @elseif ($activeType === 'group' && $activeGroup)
        <section class="card rail-card">
            <div class="rail-card__header">
                <h3>{{ $activeGroup->name }}</h3>
            </div>
            <p class="section-copy">Участников: {{ $activeGroup->memberships->count() }}</p>
            @if ($canManageGroup)
                <button type="button" class="button button--ghost button--small" data-modal-open="manage-group-modal">Управление группой</button>
            @endif
        </section>
    @else
        <section class="card rail-card">
            <div class="rail-card__header">
                <h3>Сообщения</h3>
            </div>
            <p class="section-copy">Откройте личный или групповой чат, чтобы продолжить рабочий диалог.</p>
        </section>
    @endif
@endsection

@section('content')
    @php
        $activeMembership = $activeGroup
            ? $activeGroup->memberships->firstWhere('user_id', $user->id)
            : null;
        $canManageGroup = $activeGroup && ($activeGroup->created_by === $user->id || optional($activeMembership)->role === 'owner');
    @endphp

    <div class="content-stack content-stack--wide">
        <section class="messages-layout" data-messages-page>
            <aside class="card conversations-panel">
                <div class="conversation-toolbar">
                    <p class="conversation-section__title">Диалоги</p>
                    <div class="button-group">
                        <button type="button" class="button button--ghost button--small" data-modal-open="new-chat-modal">Новый чат</button>
                        <button type="button" class="button button--primary button--small" data-modal-open="new-group-modal">Новая группа</button>
                    </div>
                </div>

                <div class="conversation-section">
                    <p class="conversation-section__title">Личные</p>
                    <div class="conversation-stack" data-conversation-list="direct">
                        @forelse ($directConversations as $conversation)
                            <a href="{{ $conversation['href'] }}"
                                class="conversation-card {{ $activeType === 'direct' && $activeDirect?->id === $conversation['id'] ? 'is-active' : '' }}"
                                data-conversation-card
                                data-conversation-type="{{ $conversation['type'] }}"
                                data-conversation-id="{{ $conversation['id'] }}">
                                <img src="{{ $conversation['avatar_url'] }}" alt="{{ $conversation['name'] }}">
                                <div class="conversation-card__body">
                                    <strong>{{ $conversation['name'] }}</strong>
                                    <span>{{ $conversation['subtitle'] }}</span>
                                </div>
                                @if ($conversation['unread_count'] > 0)
                                    <span class="nav-badge">{{ $conversation['unread_count'] }}</span>
                                @endif
                            </a>
                        @empty
                            <div class="empty-state empty-state--compact">
                                <p>Личных диалогов пока нет.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="conversation-section">
                    <p class="conversation-section__title">Группы</p>
                    <div class="conversation-stack" data-conversation-list="group">
                        @forelse ($groupConversations as $conversation)
                            <a href="{{ $conversation['href'] }}"
                                class="conversation-card {{ $activeType === 'group' && $activeGroup?->id === $conversation['id'] ? 'is-active' : '' }}"
                                data-conversation-card
                                data-conversation-type="{{ $conversation['type'] }}"
                                data-conversation-id="{{ $conversation['id'] }}">
                                <img src="{{ $conversation['avatar_url'] }}" alt="{{ $conversation['name'] }}">
                                <div class="conversation-card__body">
                                    <strong>{{ $conversation['name'] }}</strong>
                                    <span>{{ $conversation['subtitle'] }}</span>
                                </div>
                                @if ($conversation['unread_count'] > 0)
                                    <span class="nav-badge">{{ $conversation['unread_count'] }}</span>
                                @endif
                            </a>
                        @empty
                            <div class="empty-state empty-state--compact">
                                <p>Групповых чатов пока нет.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </aside>

            <section class="card chat-panel">
                @if ($activeType === 'direct' && $activeDirect)
                    <header class="chat-panel__header">
                        <div class="user-line">
                            <img src="{{ $activeDirect->avatar_url }}" alt="{{ $activeDirect->full_name }}">
                            <div>
                                <strong>{{ $activeDirect->full_name }}</strong>
                                <span>{{ $activeDirect->position }}</span>
                            </div>
                        </div>
                        <a href="{{ route('profiles.show', ['user' => $activeDirect, 'from_chat' => $activeDirect->id]) }}" class="button button--ghost button--small">Профиль</a>
                    </header>
                @elseif ($activeType === 'group' && $activeGroup)
                    <header class="chat-panel__header">
                        <div class="user-line">
                            <img src="{{ asset('uploads/' . \App\Support\AvatarDefaults::GROUP) }}" alt="{{ $activeGroup->name }}">
                            <div>
                                <strong>{{ $activeGroup->name }}</strong>
                                <span>Групповой чат · {{ $activeGroup->memberships->count() }} участников</span>
                            </div>
                        </div>
                        <div class="button-group">
                            @if ($canManageGroup)
                                <button type="button" class="button button--ghost button--small" data-modal-open="manage-group-modal">Управление</button>
                            @endif
                            @if ($activeGroup->created_by === $user->id)
                                <form action="{{ route('messages.groups.destroy', $activeGroup) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="button button--danger button--small">Удалить группу</button>
                                </form>
                            @else
                                <form action="{{ route('messages.groups.leave', $activeGroup) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="button button--ghost button--small">Покинуть</button>
                                </form>
                            @endif
                        </div>
                    </header>
                @else
                    <div class="chat-empty">
                        <h2>Выберите диалог</h2>
                        <p>Откройте личный или групповой чат слева, чтобы начать переписку.</p>
                    </div>
                @endif

                @if ($activeType && (($activeType === 'direct' && $activeDirect) || ($activeType === 'group' && $activeGroup)))
                    <div id="messages-stream"
                        class="messages-stream"
                        data-messages-stream
                        data-type="{{ $activeType }}"
                        data-id="{{ $activeType === 'direct' ? $activeDirect->id : $activeGroup->id }}"
                        data-refresh-url="{{ $activeType === 'direct' ? route('messages.direct.data', $activeDirect) : route('messages.group.data', $activeGroup) }}">
                        @foreach ($messages as $message)
                            <article class="message-bubble {{ $message['sender_id'] === $user->id ? 'message-bubble--own' : '' }}">
                                @if ($message['sender_id'] !== $user->id)
                                    <img src="{{ $message['sender_avatar_url'] }}" alt="{{ $message['sender_name'] }}">
                                @endif
                                <div class="message-bubble__content">
                                    @if ($activeType === 'group' && $message['sender_id'] !== $user->id)
                                        <strong>{{ $message['sender_name'] }}</strong>
                                    @endif
                                    @if ($message['body'])
                                        <p>{{ $message['body'] }}</p>
                                    @endif
                                    @if (! empty($message['attachments']))
                                        <div class="message-attachments">
                                            @foreach ($message['attachments'] as $attachment)
                                                @if ($attachment['file_type'] === 'image')
                                                    <a href="{{ $attachment['url'] }}" target="_blank" rel="noopener" class="message-attachment message-attachment--image">
                                                        <img src="{{ $attachment['url'] }}" alt="{{ $attachment['file_name'] }}">
                                                    </a>
                                                @else
                                                    <a href="{{ $attachment['url'] }}" class="message-attachment message-attachment--file">{{ $attachment['file_name'] }}</a>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif
                                    <span>{{ $message['created_at_label'] }}</span>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    <form id="message-form"
                        class="message-form"
                        data-message-form
                        data-submit-url="{{ $activeType === 'direct' ? route('messages.direct.store') : route('messages.group.store') }}">
                        @if ($activeType === 'direct')
                            <input type="hidden" name="receiver_id" value="{{ $activeDirect->id }}">
                        @else
                            <input type="hidden" name="group_chat_id" value="{{ $activeGroup->id }}">
                        @endif

                        <label class="file-picker file-picker--compact file-picker--icon" aria-label="Вложить файлы" title="Вложить файлы">
                            <span class="sr-only">Вложить файлы</span>
                            <span class="ui-icon ui-icon--clip" aria-hidden="true"></span>
                            <span>Вложения</span>
                            <small>Фото и документы</small>
                            <input type="file" name="attachments[]" multiple>
                        </label>
                        <input type="text" name="body" placeholder="Введите сообщение" autocomplete="off">
                        <button type="submit" class="button button--primary">Отправить</button>
                    </form>
                @endif
            </section>
        </section>
    </div>

    <div class="modal" data-modal="new-chat-modal">
        <div class="modal__dialog">
            <div class="modal__header">
                <h2>Новый чат</h2>
                <button type="button" data-modal-close>x</button>
            </div>
            <div class="modal__body stack">
                <label class="modal-search">
                    <span class="sr-only">Поиск пользователя</span>
                    <input type="search" placeholder="Найти по имени, должности или отделу" data-user-search-input>
                </label>
                <div class="modal-list" data-user-search-list>
                    @foreach ($availableUsers as $employee)
                        <a
                            href="{{ route('messages', ['chat' => $employee->id]) }}"
                            class="conversation-card"
                            data-user-search-item
                            data-search-text="{{ $employee->full_name }} {{ $employee->position }} {{ $employee->department }}">
                            <img src="{{ $employee->avatar_url }}" alt="{{ $employee->full_name }}">
                            <div class="conversation-card__body">
                                <strong>{{ $employee->full_name }}</strong>
                                <span>{{ $employee->position }} · {{ $employee->department }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
                <div class="empty-state empty-state--compact is-hidden" data-user-search-empty>
                    <p>Пользователи не найдены.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" data-modal="new-group-modal">
        <div class="modal__dialog">
            <div class="modal__header">
                <h2>Новая группа</h2>
                <button type="button" data-modal-close>x</button>
            </div>
            <div class="modal__body">
                <form action="{{ route('messages.groups.store') }}" method="POST" class="stack-form">
                    @csrf
                    <label>
                        <span>Название группы</span>
                        <input type="text" name="name" required>
                    </label>
                    <div class="checkbox-list">
                        @foreach ($availableUsers as $employee)
                            <label class="checkbox-card">
                                <input type="checkbox" name="members[]" value="{{ $employee->id }}">
                                <span>{{ $employee->full_name }} · {{ $employee->position }}</span>
                            </label>
                        @endforeach
                    </div>
                    <button type="submit" class="button button--primary">Создать группу</button>
                </form>
            </div>
        </div>
    </div>

    @if ($activeGroup && $canManageGroup)
        <div class="modal" data-modal="manage-group-modal">
            <div class="modal__dialog">
                <div class="modal__header">
                    <h2>Управление группой</h2>
                    <button type="button" data-modal-close>x</button>
                </div>
                <div class="modal__body stack">
                    <form action="{{ route('messages.groups.update', $activeGroup) }}" method="POST" class="stack-form">
                        @csrf
                        @method('PUT')
                        <label>
                            <span>Название</span>
                            <input type="text" name="name" value="{{ $activeGroup->name }}" required>
                        </label>
                        <button type="submit" class="button button--primary">Сохранить</button>
                    </form>

                    <div class="stack">
                        <h3>Участники</h3>
                        @foreach ($activeGroup->memberships as $membership)
                            <article class="member-row">
                                <div class="user-line">
                                    <img src="{{ $membership->user->avatar_url }}" alt="{{ $membership->user->full_name }}">
                                    <div>
                                        <strong>{{ $membership->user->full_name }}</strong>
                                        <span>{{ $membership->role === 'owner' ? 'Owner' : $membership->user->position }}</span>
                                    </div>
                                </div>
                                @if ($membership->user_id !== $activeGroup->created_by)
                                    <form action="{{ route('messages.groups.members.destroy', [$activeGroup, $membership->user]) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="button button--ghost button--small">Убрать</button>
                                    </form>
                                @endif
                            </article>
                        @endforeach
                    </div>

                    @if ($groupCandidates->isNotEmpty())
                        <form action="{{ route('messages.groups.members.store', $activeGroup) }}" method="POST" class="stack-form">
                            @csrf
                            <label>
                                <span>Добавить участника</span>
                                <select name="user_id">
                                    @foreach ($groupCandidates as $candidate)
                                        <option value="{{ $candidate->id }}">{{ $candidate->full_name }} · {{ $candidate->position }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <button type="submit" class="button button--primary">Добавить</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    @endif
@endsection
