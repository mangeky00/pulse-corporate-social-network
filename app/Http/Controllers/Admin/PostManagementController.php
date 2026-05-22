<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostAttachment;
use App\Support\FileUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PostManagementController extends Controller
{
    public function __construct(private readonly FileUploadService $uploadService)
    {
    }

    public function index(): View
    {
        return view('admin.posts', [
            'posts' => Post::query()
                ->with(['user', 'attachments'])
                ->withCount(['comments', 'positiveLikes as likes_count'])
                ->latest()
                ->get(),
        ]);
    }

    public function destroy(Post $post): RedirectResponse
    {
        $this->deletePost($post);

        return back()->with('message', 'Пост удалён.');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'post_ids' => ['required', 'array'],
            'post_ids.*' => ['integer', 'exists:posts,id'],
        ]);

        Post::query()
            ->with('attachments')
            ->whereIn('id', $data['post_ids'])
            ->get()
            ->each(fn (Post $post) => $this->deletePost($post));

        return back()->with('message', 'Выбранные посты удалены.');
    }

    private function deletePost(Post $post): void
    {
        $post->loadMissing('attachments');
        $post->attachments->each(fn (PostAttachment $attachment) => $this->uploadService->remove($attachment->file_path));
        $post->delete();
    }
}
