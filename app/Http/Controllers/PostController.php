<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
use App\Models\PostAttachment;
use App\Support\FileUploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PostController extends Controller
{
    public function __construct(private readonly FileUploadService $uploadService)
    {
    }

    public function index(Request $request): View
    {
        $posts = Post::query()
            ->with([
                'user',
                'attachments',
                'comments.user',
            ])
            ->withCount([
                'positiveLikes as likes_count',
                'comments',
            ])
            ->latest()
            ->get();

        $userLikes = $request->user()->likes()
            ->where('like', true)
            ->pluck('id', 'post_id');

        return view('dashboard', [
            'posts' => $posts,
            'userLikes' => $userLikes,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'body' => ['required', 'string', 'max:4000'],
            'attachments.*' => ['nullable', 'file', 'max:5120'],
        ]);

        $post = $request->user()->posts()->create([
            'body' => $data['body'],
        ]);

        $this->storeAttachments($post, $request->file('attachments', []));

        return redirect()->route('dashboard')->with('message', 'Пост опубликован.');
    }

    public function update(Request $request, Post $post): RedirectResponse
    {
        abort_unless($request->user()->is_admin, 403);

        $data = $request->validate([
            'body' => ['required', 'string', 'max:4000'],
        ]);

        $post->update([
            'body' => $data['body'],
        ]);

        return back()->with('message', 'Пост обновлён.');
    }

    public function destroy(Request $request, Post $post): RedirectResponse
    {
        abort_unless($request->user()->is_admin, 403);

        $post->load('attachments');
        $post->attachments->each(fn (PostAttachment $attachment) => $this->uploadService->remove($attachment->file_path));
        $post->delete();

        return back()->with('message', 'Пост удалён.');
    }

    public function toggleLike(Request $request, Post $post): JsonResponse
    {
        $like = Like::query()->firstOrNew([
            'user_id' => $request->user()->id,
            'post_id' => $post->id,
        ]);

        $liked = true;

        if ($like->exists && $like->like) {
            $like->delete();
            $liked = false;
        } else {
            $like->fill(['like' => true])->save();
        }

        return response()->json([
            'liked' => $liked,
            'likes_count' => $post->positiveLikes()->count(),
        ]);
    }

    public function storeComment(Request $request, Post $post): JsonResponse
    {
        $data = $request->validate([
            'body' => ['required', 'string', 'max:1000'],
        ]);

        $comment = $post->comments()->create([
            'body' => $data['body'],
            'user_id' => $request->user()->id,
        ]);

        $comment->load('user');

        return response()->json([
            'comment' => $this->serializeComment($comment),
            'comments_count' => $post->comments()->count(),
        ]);
    }

    public function destroyComment(Request $request, Comment $comment): JsonResponse
    {
        abort_unless(
            $request->user()->is_admin || $request->user()->id === $comment->user_id,
            403
        );

        $post = $comment->post;
        $comment->delete();

        return response()->json([
            'comments_count' => $post->comments()->count(),
        ]);
    }

    private function storeAttachments(Post $post, array $files): void
    {
        foreach ($files as $file) {
            if (! $file) {
                continue;
            }

            $post->attachments()->create(
                $this->uploadService->store($file, 'posts')
            );
        }
    }

    private function serializeComment(Comment $comment): array
    {
        return [
            'id' => $comment->id,
            'body' => $comment->body,
            'created_at' => $comment->created_at?->format('d.m.Y H:i'),
            'user' => [
                'id' => $comment->user->id,
                'name' => $comment->user->full_name,
                'avatar_url' => $comment->user->avatar_url,
            ],
            'can_delete' => auth()->user()?->is_admin || auth()->id() === $comment->user_id,
        ];
    }
}
