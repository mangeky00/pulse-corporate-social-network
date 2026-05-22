@extends('layouts.master')

@section('title', 'Pulse · Пользователи')

@section('rail')
    <section class="card rail-card">
        <div class="rail-card__header">
            <h3>Подсказка</h3>
        </div>
        <p class="section-copy">Создавайте сотрудников слева и редактируйте существующие карточки ниже по странице.</p>
    </section>
@endsection

@section('content')
    <div class="content-stack content-stack--narrow">
        <section class="card">
            <div class="section-heading">
                <div>
                    <p class="eyebrow">Новый сотрудник</p>
                    <h2>Добавить пользователя</h2>
                </div>
            </div>

            <form action="{{ route('admin.users.store') }}" method="POST" class="stack-form">
                @csrf
                <div class="form-grid">
                    <label><span>Имя</span><input type="text" name="first_name" required></label>
                    <label><span>Фамилия</span><input type="text" name="last_name"></label>
                    <label><span>Email</span><input type="email" name="email" required></label>
                    <label><span>Телефон</span><input type="text" name="phone"></label>
                    <label><span>Должность</span><input type="text" name="position" required></label>
                    <label><span>Отдел</span><input type="text" name="department" required></label>
                    <label>
                        <span>Роль</span>
                        <select name="role">
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                    </label>
                    <label><span>Пароль</span><input type="text" name="password" placeholder="password123 по умолчанию"></label>
                </div>
                <button type="submit" class="button button--primary">Создать</button>
            </form>
        </section>

        <section class="card">
            <div class="section-heading">
                <div>
                    <p class="eyebrow">Список</p>
                    <h2>Сотрудники</h2>
                </div>
            </div>

            <div class="stack">
                @foreach ($users as $employee)
                    <details class="details-card">
                        <summary>
                            <div class="user-line">
                                <img src="{{ $employee->avatar_url }}" alt="{{ $employee->full_name }}">
                                <div>
                                    <strong>{{ $employee->full_name }}</strong>
                                    <span>{{ $employee->email }} · {{ $employee->position }}</span>
                                </div>
                            </div>
                            <span class="pill">{{ strtoupper($employee->role) }}</span>
                        </summary>

                        <div class="details-card__content">
                            <form action="{{ route('admin.users.update', $employee) }}" method="POST" class="stack-form">
                                @csrf
                                @method('PUT')
                                <div class="form-grid">
                                    <label><span>Имя</span><input type="text" name="first_name" value="{{ $employee->first_name }}" required></label>
                                    <label><span>Фамилия</span><input type="text" name="last_name" value="{{ $employee->last_name }}"></label>
                                    <label><span>Email</span><input type="email" name="email" value="{{ $employee->email }}" required></label>
                                    <label><span>Телефон</span><input type="text" name="phone" value="{{ $employee->phone }}"></label>
                                    <label><span>Должность</span><input type="text" name="position" value="{{ $employee->position }}" required></label>
                                    <label><span>Отдел</span><input type="text" name="department" value="{{ $employee->department }}" required></label>
                                    <label>
                                        <span>Роль</span>
                                        <select name="role">
                                            <option value="user" @selected($employee->role === 'user')>User</option>
                                            <option value="admin" @selected($employee->role === 'admin')>Admin</option>
                                        </select>
                                    </label>
                                </div>
                                <div class="button-group">
                                    <button type="submit" class="button button--primary button--small">Сохранить</button>
                                </div>
                            </form>

                            <div class="button-group">
                                <form action="{{ route('admin.users.password.reset', $employee) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="button button--ghost button--small">Сбросить пароль</button>
                                </form>
                                <form action="{{ route('admin.users.destroy', $employee) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="button button--danger button--small">Удалить</button>
                                </form>
                            </div>
                        </div>
                    </details>
                @endforeach
            </div>
        </section>
    </div>
@endsection
