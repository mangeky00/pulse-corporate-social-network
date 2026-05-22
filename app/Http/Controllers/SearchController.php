<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function index(Request $request): View
    {
        $query = trim((string) $request->string('q'));

        $users = collect();
        $posts = collect();

        if ($query !== '') {
            $users = User::query()
                ->where(function ($builder) use ($query) {
                    $builder
                        ->where('first_name', 'like', "%{$query}%")
                        ->orWhere('last_name', 'like', "%{$query}%")
                        ->orWhere('email', 'like', "%{$query}%")
                        ->orWhere('position', 'like', "%{$query}%")
                        ->orWhere('department', 'like', "%{$query}%");
                })
                ->orderBy('first_name')
                ->limit(20)
                ->get();

            $posts = Post::query()
                ->with('user')
                ->where(function ($builder) use ($query) {
                    $builder
                        ->where('body', 'like', "%{$query}%")
                        ->orWhereHas('user', function ($userQuery) use ($query) {
                            $userQuery
                                ->where('first_name', 'like', "%{$query}%")
                                ->orWhere('last_name', 'like', "%{$query}%");
                        });
                })
                ->latest()
                ->limit(20)
                ->get();
        }

        return view('search.index', [
            'query' => $query,
            'users' => $users,
            'posts' => $posts,
        ]);
    }
}
