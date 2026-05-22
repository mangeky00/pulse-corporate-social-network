<?php

namespace App\Http\Controllers;

use App\Events\ChatMessageBroadcast;
use App\Models\GroupChat;
use App\Models\GroupMember;
use App\Models\GroupMessage;
use App\Models\Message;
use App\Models\User;
use App\Support\AvatarDefaults;
use App\Support\FileUploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function __construct(private readonly FileUploadService $uploadService)
    {
    }

    public function index(Request $request): View
    {
        $user = $request->user();
        $directConversations = $this->directConversations($user);
        $groupConversations = $this->groupConversations($user);

        $activeType = null;
        $activeDirect = null;
        $activeGroup = null;
        $messages = [];

        if ($request->filled('chat')) {
            $activeDirect = User::query()->findOrFail($request->integer('chat'));
            abort_if($activeDirect->id === $user->id, 404);

            $this->markDirectMessagesAsRead($user, $activeDirect);
            $messages = $this->serializeDirectMessages($user, $activeDirect);
            $activeType = 'direct';
        } elseif ($request->filled('group')) {
            $activeGroup = GroupChat::query()
                ->with(['creator', 'memberships.user'])
                ->findOrFail($request->integer('group'));

            $membership = $activeGroup->memberships()
                ->where('user_id', $user->id)
                ->first();

            abort_unless($membership, 403);

            $membership->forceFill(['last_read_at' => now()])->save();
            $messages = $this->serializeGroupMessages($activeGroup);
            $activeType = 'group';
        }

        $availableUsers = User::query()
            ->whereKeyNot($user->id)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $groupCandidates = collect();

        if ($activeGroup) {
            $existingMemberIds = $activeGroup->memberships->pluck('user_id');
            $groupCandidates = $availableUsers->whereNotIn('id', $existingMemberIds);
        }

        return view('messages.index', [
            'directConversations' => $directConversations,
            'groupConversations' => $groupConversations,
            'availableUsers' => $availableUsers,
            'groupCandidates' => $groupCandidates,
            'activeType' => $activeType,
            'activeDirect' => $activeDirect,
            'activeGroup' => $activeGroup,
            'messages' => $messages,
            'user' => $user,
        ]);
    }

    public function conversations(Request $request): JsonResponse
    {
        $conversations = collect($this->directConversations($request->user()))
            ->merge($this->groupConversations($request->user()))
            ->sortByDesc('last_message_at')
            ->values();

        return response()->json($conversations);
    }

    public function directMessagesData(Request $request, User $user): JsonResponse
    {
        abort_if($user->id === $request->user()->id, 404);

        $this->markDirectMessagesAsRead($request->user(), $user);

        return response()->json([
            'messages' => $this->serializeDirectMessages($request->user(), $user),
        ]);
    }

    public function groupMessagesData(Request $request, GroupChat $groupChat): JsonResponse
    {
        $membership = $groupChat->memberships()->where('user_id', $request->user()->id)->first();
        abort_unless($membership, 403);

        $membership->forceFill(['last_read_at' => now()])->save();

        return response()->json([
            'messages' => $this->serializeGroupMessages($groupChat),
        ]);
    }

    public function storeDirectMessage(Request $request): JsonResponse
    {
        $data = $request->validate([
            'receiver_id' => ['required', 'integer', 'different:sender_id', 'exists:users,id'],
            'body' => ['nullable', 'string', 'max:3000'],
            'attachments.*' => ['nullable', 'file', 'max:5120'],
        ]);

        abort_if(blank($data['body'] ?? null) && ! $request->hasFile('attachments'), 422, 'Введите сообщение или добавьте файл.');

        $sender = $request->user();
        $receiver = User::query()->findOrFail($data['receiver_id']);

        $message = Message::query()->create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'body' => $data['body'] ?? null,
        ]);

        $this->storeDirectAttachments($message, $request->file('attachments', []));

        $message->load(['sender', 'attachments']);

        $this->dispatchBroadcastSafely(new ChatMessageBroadcast(
            [$receiver->id],
            [
                'type' => 'direct',
                'conversation_id' => $sender->id,
                'message' => $this->serializeDirectMessage($message),
                'conversation' => $this->buildDirectConversationCard(
                    $receiver,
                    $sender,
                    $message,
                    Message::query()
                        ->where('sender_id', $sender->id)
                        ->where('receiver_id', $receiver->id)
                        ->whereNull('read_at')
                        ->count()
                ),
            ],
        ));

        return response()->json([
            'message' => $this->serializeDirectMessage($message),
            'conversation' => $this->buildDirectConversationCard($sender, $receiver, $message, 0),
        ]);
    }

    public function storeGroupMessage(Request $request): JsonResponse
    {
        $data = $request->validate([
            'group_chat_id' => ['required', 'integer', 'exists:group_chats,id'],
            'body' => ['nullable', 'string', 'max:3000'],
            'attachments.*' => ['nullable', 'file', 'max:5120'],
        ]);

        abort_if(blank($data['body'] ?? null) && ! $request->hasFile('attachments'), 422, 'Введите сообщение или добавьте файл.');

        $group = GroupChat::query()->with('memberships')->findOrFail($data['group_chat_id']);
        $membership = $group->memberships->firstWhere('user_id', $request->user()->id);
        abort_unless($membership, 403);

        $message = $group->messages()->create([
            'sender_id' => $request->user()->id,
            'body' => $data['body'] ?? null,
        ]);

        $this->storeGroupAttachments($message, $request->file('attachments', []));

        $membership->forceFill(['last_read_at' => now()])->save();
        $message->load(['sender', 'attachments']);

        $recipientIds = $group->memberships
            ->pluck('user_id')
            ->reject(fn (int $id) => $id === $request->user()->id)
            ->values()
            ->all();

        if ($recipientIds !== []) {
            $conversationPayload = collect($recipientIds)->mapWithKeys(function (int $recipientId) use ($group, $message) {
                $recipient = User::query()->find($recipientId);
                if (! $recipient) {
                    return [];
                }

                return [$recipientId => $this->buildGroupConversationCard($recipient, $group, $message)];
            })->all();

            $this->dispatchBroadcastSafely(new ChatMessageBroadcast(
                $recipientIds,
                [
                    'type' => 'group',
                    'conversation_id' => $group->id,
                    'message' => $this->serializeGroupMessage($message),
                    'conversation_payload' => $conversationPayload,
                ],
            ));
        }

        return response()->json([
            'message' => $this->serializeGroupMessage($message),
            'conversation' => $this->buildGroupConversationCard($request->user(), $group, $message),
        ]);
    }

    public function storeGroup(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'members' => ['array'],
            'members.*' => ['integer', 'exists:users,id'],
        ]);

        $group = GroupChat::query()->create([
            'name' => $data['name'],
            'created_by' => $request->user()->id,
        ]);

        $group->memberships()->create([
            'user_id' => $request->user()->id,
            'role' => 'owner',
            'last_read_at' => now(),
        ]);

        foreach (collect($data['members'] ?? [])->unique()->reject(fn (int $id) => $id === $request->user()->id) as $memberId) {
            $group->memberships()->create([
                'user_id' => $memberId,
                'role' => 'member',
                'last_read_at' => now(),
            ]);
        }

        return redirect()->route('messages', ['group' => $group->id])->with('message', 'Группа создана.');
    }

    public function renameGroup(Request $request, GroupChat $groupChat): RedirectResponse
    {
        $this->authorizeGroupOwner($groupChat, $request->user());

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
        ]);

        $groupChat->update([
            'name' => $data['name'],
        ]);

        return back()->with('message', 'Название группы обновлено.');
    }

    public function addGroupMember(Request $request, GroupChat $groupChat): RedirectResponse
    {
        $this->authorizeGroupOwner($groupChat, $request->user());

        $data = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        if (! $groupChat->memberships()->where('user_id', $data['user_id'])->exists()) {
            $groupChat->memberships()->create([
                'user_id' => $data['user_id'],
                'role' => 'member',
                'last_read_at' => now(),
            ]);
        }

        return back()->with('message', 'Участник добавлен в группу.');
    }

    public function removeGroupMember(Request $request, GroupChat $groupChat, User $user): RedirectResponse
    {
        $this->authorizeGroupOwner($groupChat, $request->user());

        if ($user->id === $groupChat->created_by) {
            return back()->withErrors(['group' => 'Создателя группы удалить нельзя.']);
        }

        $groupChat->memberships()->where('user_id', $user->id)->delete();

        return back()->with('message', 'Участник удалён из группы.');
    }

    public function leaveGroup(Request $request, GroupChat $groupChat): RedirectResponse
    {
        if ($groupChat->created_by === $request->user()->id) {
            return back()->withErrors(['group' => 'Создатель группы должен удалить её, а не покидать.']);
        }

        $groupChat->memberships()->where('user_id', $request->user()->id)->delete();

        return redirect()->route('messages')->with('message', 'Вы покинули группу.');
    }

    public function destroyGroup(Request $request, GroupChat $groupChat): RedirectResponse
    {
        abort_unless($groupChat->created_by === $request->user()->id, 403);

        $groupChat->messages()->with('attachments')->get()->each(function (GroupMessage $message) {
            $message->attachments->each(fn ($attachment) => $this->uploadService->remove($attachment->file_path));
        });

        $groupChat->delete();

        return redirect()->route('messages')->with('message', 'Группа удалена.');
    }

    private function directConversations(User $user): array
    {
        $allMessages = Message::query()
            ->with(['sender', 'receiver'])
            ->where(fn ($query) => $query
                ->where('sender_id', $user->id)
                ->orWhere('receiver_id', $user->id))
            ->latest()
            ->get();

        $unread = Message::query()
            ->selectRaw('sender_id, COUNT(*) as total')
            ->where('receiver_id', $user->id)
            ->whereNull('read_at')
            ->groupBy('sender_id')
            ->pluck('total', 'sender_id');

        $cards = [];

        foreach ($allMessages as $message) {
            $other = $message->sender_id === $user->id ? $message->receiver : $message->sender;

            if (! $other || isset($cards[$other->id])) {
                continue;
            }

            $cards[$other->id] = $this->buildDirectConversationCard(
                $user,
                $other,
                $message,
                (int) ($unread[$other->id] ?? 0),
            );
        }

        return array_values($cards);
    }

    private function groupConversations(User $user): array
    {
        $groups = $user->groups()->with(['creator'])->get();

        return $groups->map(function (GroupChat $group) use ($user) {
            $lastMessage = $group->messages()->with('sender')->latest()->first();

            return $this->buildGroupConversationCard($user, $group, $lastMessage);
        })->sortByDesc('last_message_at')->values()->all();
    }

    private function buildDirectConversationCard(User $viewer, User $other, ?Message $lastMessage, int $unreadCount): array
    {
        return [
            'type' => 'direct',
            'id' => $other->id,
            'name' => $other->full_name,
            'subtitle' => $other->position,
            'avatar_url' => $other->avatar_url,
            'last_message_preview' => $lastMessage?->body ?: ($lastMessage && $lastMessage->attachments()->exists() ? 'Вложение' : 'Начните диалог'),
            'last_message_at' => optional($lastMessage?->created_at)->toIso8601String(),
            'last_message_at_label' => $lastMessage?->created_at?->format('d.m H:i'),
            'unread_count' => $unreadCount,
            'href' => route('messages', ['chat' => $other->id]),
        ];
    }

    private function buildGroupConversationCard(User $viewer, GroupChat $group, ?GroupMessage $lastMessage): array
    {
        $membership = $group->memberships()->where('user_id', $viewer->id)->first();

        $unreadCount = GroupMessage::query()
            ->where('group_chat_id', $group->id)
            ->where('sender_id', '!=', $viewer->id)
            ->when(
                $membership?->last_read_at,
                fn ($query, $lastReadAt) => $query->where('created_at', '>', $lastReadAt)
            )
            ->count();

        return [
            'type' => 'group',
            'id' => $group->id,
            'name' => $group->name,
            'subtitle' => 'Групповой чат',
            'avatar_url' => asset('uploads/'.AvatarDefaults::GROUP),
            'last_message_preview' => $lastMessage?->body ?: ($lastMessage && $lastMessage->attachments()->exists() ? 'Вложение' : 'Создайте первое сообщение'),
            'last_message_at' => optional($lastMessage?->created_at)->toIso8601String(),
            'last_message_at_label' => $lastMessage?->created_at?->format('d.m H:i'),
            'unread_count' => $unreadCount,
            'href' => route('messages', ['group' => $group->id]),
        ];
    }

    private function markDirectMessagesAsRead(User $viewer, User $other): void
    {
        Message::query()
            ->where('sender_id', $other->id)
            ->where('receiver_id', $viewer->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    private function serializeDirectMessages(User $viewer, User $other): array
    {
        return Message::query()
            ->with(['sender', 'attachments'])
            ->where(function ($query) use ($viewer, $other) {
                $query
                    ->where('sender_id', $viewer->id)
                    ->where('receiver_id', $other->id);
            })
            ->orWhere(function ($query) use ($viewer, $other) {
                $query
                    ->where('sender_id', $other->id)
                    ->where('receiver_id', $viewer->id);
            })
            ->oldest()
            ->get()
            ->map(fn (Message $message) => $this->serializeDirectMessage($message))
            ->all();
    }

    private function serializeGroupMessages(GroupChat $groupChat): array
    {
        return $groupChat->messages()
            ->with(['sender', 'attachments'])
            ->oldest()
            ->get()
            ->map(fn (GroupMessage $message) => $this->serializeGroupMessage($message))
            ->all();
    }

    private function serializeDirectMessage(Message $message): array
    {
        return [
            'id' => $message->id,
            'body' => $message->body,
            'sender_id' => $message->sender_id,
            'sender_name' => $message->sender->full_name,
            'sender_avatar_url' => $message->sender->avatar_url,
            'created_at' => $message->created_at?->toIso8601String(),
            'created_at_label' => $message->created_at?->format('H:i'),
            'attachments' => $message->attachments->map(fn ($attachment) => [
                'id' => $attachment->id,
                'file_name' => $attachment->file_name,
                'file_type' => $attachment->file_type,
                'url' => $attachment->url,
            ])->all(),
        ];
    }

    private function serializeGroupMessage(GroupMessage $message): array
    {
        return [
            'id' => $message->id,
            'body' => $message->body,
            'sender_id' => $message->sender_id,
            'sender_name' => $message->sender->full_name,
            'sender_avatar_url' => $message->sender->avatar_url,
            'created_at' => $message->created_at?->toIso8601String(),
            'created_at_label' => $message->created_at?->format('H:i'),
            'attachments' => $message->attachments->map(fn ($attachment) => [
                'id' => $attachment->id,
                'file_name' => $attachment->file_name,
                'file_type' => $attachment->file_type,
                'url' => $attachment->url,
            ])->all(),
        ];
    }

    private function storeDirectAttachments(Message $message, array $files): void
    {
        foreach ($files as $file) {
            if (! $file) {
                continue;
            }

            $message->attachments()->create(
                $this->uploadService->store($file, 'messages/direct')
            );
        }
    }

    private function storeGroupAttachments(GroupMessage $message, array $files): void
    {
        foreach ($files as $file) {
            if (! $file) {
                continue;
            }

            $message->attachments()->create(
                $this->uploadService->store($file, 'messages/groups')
            );
        }
    }

    private function authorizeGroupOwner(GroupChat $groupChat, User $user): void
    {
        $membership = $groupChat->memberships()->where('user_id', $user->id)->first();

        abort_unless(
            $groupChat->created_by === $user->id || $membership?->role === 'owner',
            403
        );
    }

    private function dispatchBroadcastSafely(ChatMessageBroadcast $event): void
    {
        try {
            event($event);
        } catch (\Throwable $exception) {
            report($exception);
        }
    }
}
