<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PostManagementController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'showLogin'])->name('home');

Route::middleware('guest')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/setup-admin', [AuthController::class, 'setupAdmin'])->name('setup-admin');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [PostController::class, 'index'])->name('dashboard');
    Route::post('/posts', [PostController::class, 'store'])->middleware('admin')->name('posts.store');
    Route::put('/posts/{post}', [PostController::class, 'update'])->middleware('admin')->name('posts.update');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->middleware('admin')->name('posts.destroy');
    Route::post('/posts/{post}/like', [PostController::class, 'toggleLike'])->name('posts.like');
    Route::post('/posts/{post}/comments', [PostController::class, 'storeComment'])->name('posts.comments.store');
    Route::delete('/comments/{comment}', [PostController::class, 'destroyComment'])->name('posts.comments.destroy');

    Route::get('/account', [AccountController::class, 'show'])->name('account');
    Route::put('/account/profile', [AccountController::class, 'updateProfile'])->name('account.profile');
    Route::put('/account/password', [AccountController::class, 'updatePassword'])->name('account.password');

    Route::get('/profiles/{user}', [ProfileController::class, 'show'])->name('profiles.show');
    Route::get('/search', [SearchController::class, 'index'])->name('search');

    Route::get('/messages', [MessageController::class, 'index'])->name('messages');
    Route::get('/messages/conversations/data', [MessageController::class, 'conversations'])->name('messages.conversations');
    Route::get('/messages/direct/{user}/data', [MessageController::class, 'directMessagesData'])->name('messages.direct.data');
    Route::get('/messages/group/{groupChat}/data', [MessageController::class, 'groupMessagesData'])->name('messages.group.data');
    Route::post('/messages/direct', [MessageController::class, 'storeDirectMessage'])->name('messages.direct.store');
    Route::post('/messages/group', [MessageController::class, 'storeGroupMessage'])->name('messages.group.store');
    Route::post('/messages/groups', [MessageController::class, 'storeGroup'])->name('messages.groups.store');
    Route::put('/messages/groups/{groupChat}', [MessageController::class, 'renameGroup'])->name('messages.groups.update');
    Route::post('/messages/groups/{groupChat}/members', [MessageController::class, 'addGroupMember'])->name('messages.groups.members.store');
    Route::delete('/messages/groups/{groupChat}/members/{user}', [MessageController::class, 'removeGroupMember'])->name('messages.groups.members.destroy');
    Route::delete('/messages/groups/{groupChat}/leave', [MessageController::class, 'leaveGroup'])->name('messages.groups.leave');
    Route::delete('/messages/groups/{groupChat}', [MessageController::class, 'destroyGroup'])->name('messages.groups.destroy');

    Route::prefix('admin')->middleware('admin')->name('admin.')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
        Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
        Route::put('/users/{user}/reset-password', [UserManagementController::class, 'resetPassword'])->name('users.password.reset');

        Route::get('/posts', [PostManagementController::class, 'index'])->name('posts.index');
        Route::delete('/posts/{post}', [PostManagementController::class, 'destroy'])->name('posts.destroy');
        Route::delete('/posts', [PostManagementController::class, 'bulkDestroy'])->name('posts.bulk-destroy');
    });
});
