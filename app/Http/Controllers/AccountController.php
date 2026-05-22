<?php

namespace App\Http\Controllers;

use App\Support\FileUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function __construct(private readonly FileUploadService $uploadService)
    {
    }

    public function show(Request $request): View
    {
        return view('account', [
            'user' => $request->user(),
        ]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:120'],
            'last_name' => ['nullable', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:30'],
            'avatar' => ['nullable', 'file', 'max:5120'],
        ]);

        $user = $request->user();
        $user->fill([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'] ?? '',
            'phone' => $data['phone'] ?? null,
        ]);

        if ($request->hasFile('avatar')) {
            $oldAvatar = $user->avatar;
            $user->avatar = $this->uploadService->storeAvatar($request->file('avatar'), $user->id);
            $this->uploadService->remove($oldAvatar);
        }

        $user->save();

        return back()->with('message', 'Профиль обновлён.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = $request->user();

        if (! Hash::check($data['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Текущий пароль введён неверно.']);
        }

        $user->forceFill([
            'password' => Hash::make($data['password']),
        ])->save();

        return back()->with('message', 'Пароль обновлён.');
    }
}
