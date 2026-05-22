<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\GroupChat;
use App\Models\Message;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $recentActivity = collect()
            ->merge(
                Post::query()->with('user')->latest()->take(3)->get()->map(fn (Post $post) => [
                    'type' => 'Пост',
                    'user' => $post->user,
                    'text' => 'опубликовал новость',
                    'created_at' => $post->created_at,
                ])
            )
            ->merge(
                Comment::query()->with('user')->latest()->take(3)->get()->map(fn (Comment $comment) => [
                    'type' => 'Комментарий',
                    'user' => $comment->user,
                    'text' => 'оставил комментарий',
                    'created_at' => $comment->created_at,
                ])
            )
            ->merge(
                Message::query()->with('sender')->latest()->take(3)->get()->map(fn (Message $message) => [
                    'type' => 'Сообщение',
                    'user' => $message->sender,
                    'text' => 'отправил сообщение',
                    'created_at' => $message->created_at,
                ])
            )
            ->sortByDesc('created_at')
            ->take(8)
            ->values();

        return view('admin.dashboard', [
            'stats' => [
                'users' => User::query()->count(),
                'posts' => Post::query()->count(),
                'comments' => Comment::query()->count(),
                'messages' => Message::query()->count(),
                'groups' => GroupChat::query()->count(),
            ],
            'recentActivity' => $recentActivity,
        ]);
    }
}
