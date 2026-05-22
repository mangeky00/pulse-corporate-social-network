<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use App\Support\AvatarDefaults;
use App\Support\FileUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function __construct(private readonly FileUploadService $uploadService)
    {
    }

    public function index(): View
    {
        return view('admin.users', [
            'users' => User::query()->latest()->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:120'],
            'last_name' => ['nullable', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'position' => ['required', 'string', 'max:120'],
            'department' => ['required', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:30'],
            'role' => ['required', 'in:user,admin'],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        User::query()->create([
            ...$data,
            'password' => Hash::make($data['password'] ?: 'password123'),
            'avatar' => AvatarDefaults::USER,
        ]);

        return back()->with('message', 'Пользователь создан.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:120'],
            'last_name' => ['nullable', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'position' => ['required', 'string', 'max:120'],
            'department' => ['required', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:30'],
            'role' => ['required', 'in:user,admin'],
        ]);

        if ($user->id === $request->user()->id && $data['role'] !== 'admin') {
            return back()->withErrors(['role' => 'Нельзя снять роль администратора у текущей сессии.']);
        }

        $user->update($data);

        return back()->with('message', 'Пользователь обновлён.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return back()->withErrors(['user' => 'Нельзя удалить текущего пользователя.']);
        }

        if ($user->role === 'admin' && User::query()->where('role', 'admin')->count() <= 1) {
            return back()->withErrors(['user' => 'В системе должен остаться хотя бы один администратор.']);
        }

        DB::transaction(function () use ($user) {
            Comment::query()->where('user_id', $user->id)->delete();
            Like::query()->where('user_id', $user->id)->delete();

            Post::query()->with('attachments')->where('user_id', $user->id)->get()->each(function (Post $post) {
                $post->attachments->each(fn ($attachment) => $this->uploadService->remove($attachment->file_path));
                $post->delete();
            });

            $this->uploadService->remove($user->avatar);
            $user->delete();
        });

        return back()->with('message', 'Пользователь удалён.');
    }

    public function resetPassword(User $user): RedirectResponse
    {
        $user->forceFill([
            'password' => Hash::make('password123'),
        ])->save();

        return back()->with('message', 'Пароль сброшен на password123.');
    }
}
