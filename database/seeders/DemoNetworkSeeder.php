<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\GroupChat;
use App\Models\Like;
use App\Models\Message;
use App\Models\Post;
use App\Models\User;
use App\Support\AvatarDefaults;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DemoNetworkSeeder extends Seeder
{
    public const DEFAULT_PASSWORD = 'password123';

    private const LEGACY_POST_BODIES = [
        'Town hall starts on Thursday at 16:00 in the main conference room. Please submit questions in advance.',
        'The new onboarding checklist is live in the knowledge base. Managers should review it with every new teammate.',
        'Friday breakfast moves to the third-floor kitchen this week. Dietary preferences are collected in the office form.',
    ];

    private const LEGACY_DIRECT_MESSAGES = [
        'Please draft the launch update before 14:00.',
        'Draft is ready. I only need the final metric from finance.',
        'Can you review the Q2 roadmap before lunch?',
        'Yes. I will leave notes in the document in 20 minutes.',
        'Please confirm headcount for the May hiring plan.',
        'Confirmed. I will send the signed sheet after the client call.',
        'Let us sync on the policy update after the town hall.',
    ];

    private const LEGACY_GROUP_NAMES = [
        'Launch Crew',
        'People Ops',
    ];

    public function run(): void
    {
        DB::transaction(function () {
            $users = $this->seedUsers();

            $this->cleanupLegacyDemoContent($users);

            $posts = $this->seedPosts($users);

            $this->seedLikes($users, $posts);
            $this->seedComments($users, $posts);
            $this->seedDirectMessages($users);
            $this->seedGroups($users);
        });
    }

    /**
     * @return array<string, User>
     */
    private function seedUsers(): array
    {
        $passwordHash = Hash::make(self::DEFAULT_PASSWORD);

        $definitions = [
            'admin' => [
                'first_name' => 'Александр',
                'last_name' => 'Картер',
                'email' => 'admin@socnetwork.local',
                'position' => 'Директор по операциям',
                'department' => 'Администрация',
                'phone' => '+7 (495) 100-00-00',
                'role' => 'admin',
                'last_login' => $this->at(2026, 4, 27, 12, 45),
            ],
            'olivia' => [
                'first_name' => 'Оливия',
                'last_name' => 'Брукс',
                'email' => 'olivia.brooks@socnetwork.local',
                'position' => 'Продуктовый менеджер',
                'department' => 'Продукт',
                'phone' => '+7 (495) 100-00-01',
                'role' => 'user',
                'last_login' => $this->at(2026, 4, 27, 11, 50),
            ],
            'max' => [
                'first_name' => 'Макс',
                'last_name' => 'Тернер',
                'email' => 'max.turner@socnetwork.local',
                'position' => 'Backend-разработчик',
                'department' => 'Разработка',
                'phone' => '+7 (495) 100-00-02',
                'role' => 'user',
                'last_login' => $this->at(2026, 4, 27, 11, 5),
            ],
            'nina' => [
                'first_name' => 'Нина',
                'last_name' => 'Парк',
                'email' => 'nina.park@socnetwork.local',
                'position' => 'HR-менеджер',
                'department' => 'HR',
                'phone' => '+7 (495) 100-00-03',
                'role' => 'user',
                'last_login' => $this->at(2026, 4, 27, 10, 40),
            ],
            'leo' => [
                'first_name' => 'Лео',
                'last_name' => 'Сандерс',
                'email' => 'leo.sanders@socnetwork.local',
                'position' => 'Продуктовый дизайнер',
                'department' => 'Дизайн',
                'phone' => '+7 (495) 100-00-04',
                'role' => 'user',
                'last_login' => $this->at(2026, 4, 27, 10, 10),
            ],
            'emma' => [
                'first_name' => 'Эмма',
                'last_name' => 'Хьюз',
                'email' => 'emma.hughes@socnetwork.local',
                'position' => 'Руководитель продаж',
                'department' => 'Продажи',
                'phone' => '+7 (495) 100-00-05',
                'role' => 'user',
                'last_login' => $this->at(2026, 4, 27, 9, 35),
            ],
        ];

        $users = [];

        foreach ($definitions as $key => $definition) {
            $user = User::query()->firstOrNew(['email' => $definition['email']]);
            $user->fill([
                'first_name' => $definition['first_name'],
                'last_name' => $definition['last_name'],
                'position' => $definition['position'],
                'department' => $definition['department'],
                'phone' => $definition['phone'],
                'role' => $definition['role'],
                'avatar' => AvatarDefaults::USER,
                'last_login' => $definition['last_login'],
            ]);
            $user->email = $definition['email'];
            $user->password = $passwordHash;
            $user->save();

            $users[$key] = $user;
        }

        return $users;
    }

    /**
     * @param  array<string, User>  $users
     * @return array<string, Post>
     */
    private function seedPosts(array $users): array
    {
        $definitions = [
            'town_hall' => [
                'body' => 'В четверг в 16:00 проводим общий созвон в большом конференц-зале. Вопросы к встрече можно прислать заранее.',
                'created_at' => $this->at(2026, 4, 23, 16, 0),
            ],
            'onboarding' => [
                'body' => 'Обновили чек-лист адаптации в базе знаний. Руководителям нужно пройти его с каждым новым сотрудником в первую неделю.',
                'created_at' => $this->at(2026, 4, 25, 10, 30),
            ],
            'breakfast' => [
                'body' => 'В эту пятницу командный завтрак пройдет на кухне третьего этажа. Пожелания по меню собираем в офисной форме до вечера.',
                'created_at' => $this->at(2026, 4, 26, 9, 15),
            ],
        ];

        $posts = [];

        foreach ($definitions as $key => $definition) {
            $timestamp = $this->timestamp($definition['created_at']);

            DB::table('posts')->updateOrInsert(
                [
                    'user_id' => $users['admin']->id,
                    'created_at' => $timestamp,
                ],
                [
                    'body' => $definition['body'],
                    'updated_at' => $timestamp,
                ],
            );

            $posts[$key] = Post::query()
                ->where('user_id', $users['admin']->id)
                ->where('created_at', $timestamp)
                ->firstOrFail();
        }

        return $posts;
    }

    /**
     * @param  array<string, User>  $users
     * @param  array<string, Post>  $posts
     */
    private function seedLikes(array $users, array $posts): void
    {
        $likes = [
            [$users['olivia']->id, $posts['town_hall']->id],
            [$users['max']->id, $posts['town_hall']->id],
            [$users['nina']->id, $posts['town_hall']->id],
            [$users['leo']->id, $posts['town_hall']->id],
            [$users['max']->id, $posts['onboarding']->id],
            [$users['emma']->id, $posts['onboarding']->id],
            [$users['leo']->id, $posts['onboarding']->id],
            [$users['olivia']->id, $posts['breakfast']->id],
        ];

        foreach ($likes as [$userId, $postId]) {
            Like::query()->updateOrCreate(
                [
                    'user_id' => $userId,
                    'post_id' => $postId,
                ],
                [
                    'like' => true,
                ],
            );
        }
    }

    /**
     * @param  array<string, User>  $users
     * @param  array<string, Post>  $posts
     */
    private function seedComments(array $users, array $posts): void
    {
        $comments = [
            [$posts['town_hall']->id, $users['olivia']->id, 'Соберу вопросы от продуктовой команды и пришлю единым списком к среде.', $this->at(2026, 4, 23, 17, 5)],
            [$posts['town_hall']->id, $users['max']->id, 'Со стороны разработки подготовим короткий статус по миграции.', $this->at(2026, 4, 23, 18, 10)],
            [$posts['onboarding']->id, $users['nina']->id, 'Чек-лист для новичков уже обновила и добавила блок про документы.', $this->at(2026, 4, 25, 13, 30)],
            [$posts['onboarding']->id, $users['emma']->id, 'Для продаж еще нужен актуальный линк на презентацию для онбординга.', $this->at(2026, 4, 25, 15, 0)],
            [$posts['breakfast']->id, $users['leo']->id, 'Распечатаю новые таблички для кухни к утру пятницы.', $this->at(2026, 4, 26, 10, 20)],
            [$posts['breakfast']->id, $users['olivia']->id, 'Спасибо, добавлю это в еженедельный дайджест.', $this->at(2026, 4, 26, 11, 10)],
        ];

        foreach ($comments as [$postId, $userId, $body, $createdAt]) {
            $timestamp = $this->timestamp($createdAt);

            DB::table('comments')->updateOrInsert(
                [
                    'post_id' => $postId,
                    'user_id' => $userId,
                    'created_at' => $timestamp,
                ],
                [
                    'body' => $body,
                    'updated_at' => $timestamp,
                ],
            );
        }
    }

    /**
     * @param  array<string, User>  $users
     */
    private function seedDirectMessages(array $users): void
    {
        $messages = [
            [$users['admin']->id, $users['olivia']->id, 'Подготовь, пожалуйста, анонс запуска до 14:00.', $this->at(2026, 4, 27, 8, 30), null],
            [$users['olivia']->id, $users['admin']->id, 'Черновик готов. Жду только финальную цифру от финансов.', $this->at(2026, 4, 27, 9, 10), $this->at(2026, 4, 27, 9, 45)],
            [$users['olivia']->id, $users['max']->id, 'Посмотри, пожалуйста, роадмап на второй квартал до обеда.', $this->at(2026, 4, 27, 10, 25), null],
            [$users['max']->id, $users['olivia']->id, 'Да, через двадцать минут оставлю комментарии в документе.', $this->at(2026, 4, 27, 10, 55), null],
            [$users['nina']->id, $users['emma']->id, 'Подтверди, пожалуйста, численность по плану найма на май.', $this->at(2026, 4, 27, 9, 40), null],
            [$users['emma']->id, $users['nina']->id, 'Подтвердила. Отправлю подписанный лист сразу после звонка с клиентом.', $this->at(2026, 4, 27, 10, 5), $this->at(2026, 4, 27, 10, 20)],
            [$users['admin']->id, $users['nina']->id, 'После общего созвона нужно синхронизироваться по обновлению регламента.', $this->at(2026, 4, 27, 11, 15), null],
        ];

        foreach ($messages as [$senderId, $receiverId, $body, $createdAt, $readAt]) {
            $timestamp = $this->timestamp($createdAt);

            DB::table('messages')->updateOrInsert(
                [
                    'sender_id' => $senderId,
                    'receiver_id' => $receiverId,
                    'created_at' => $timestamp,
                ],
                [
                    'body' => $body,
                    'read_at' => $readAt ? $this->timestamp($readAt) : null,
                    'updated_at' => $timestamp,
                ],
            );
        }
    }

    /**
     * @param  array<string, User>  $users
     */
    private function seedGroups(array $users): void
    {
        $definitions = [
            'launch' => [
                'name' => 'Запуск портала',
                'created_by' => $users['admin']->id,
                'created_at' => $this->at(2026, 4, 27, 8, 0),
                'members' => [
                    [$users['admin']->id, 'owner', $this->at(2026, 4, 27, 12, 20)],
                    [$users['olivia']->id, 'member', $this->at(2026, 4, 27, 11, 0)],
                    [$users['max']->id, 'member', $this->at(2026, 4, 27, 10, 30)],
                    [$users['leo']->id, 'member', $this->at(2026, 4, 27, 10, 50)],
                ],
                'messages' => [
                    [$users['olivia']->id, 'Обновленный текст анонса лежит в общей папке.', $this->at(2026, 4, 27, 10, 15)],
                    [$users['leo']->id, 'Дизайн финального баннера готов, можно отдавать в публикацию.', $this->at(2026, 4, 27, 10, 45)],
                    [$users['max']->id, 'По API блокеров нет, чек-лист закрыт.', $this->at(2026, 4, 27, 11, 10)],
                ],
            ],
            'people_ops' => [
                'name' => 'HR и адаптация',
                'created_by' => $users['nina']->id,
                'created_at' => $this->at(2026, 4, 27, 8, 15),
                'members' => [
                    [$users['nina']->id, 'owner', $this->at(2026, 4, 27, 12, 10)],
                    [$users['admin']->id, 'member', $this->at(2026, 4, 27, 9, 55)],
                    [$users['emma']->id, 'member', $this->at(2026, 4, 27, 11, 25)],
                ],
                'messages' => [
                    [$users['nina']->id, 'Новая версия положения об отпусках готова к согласованию.', $this->at(2026, 4, 27, 9, 55)],
                    [$users['emma']->id, 'Команда продаж посмотрела документ, осталась одна правка по формулировке.', $this->at(2026, 4, 27, 10, 35)],
                ],
            ],
        ];

        foreach ($definitions as $definition) {
            $groupTimestamp = $this->timestamp($definition['created_at']);

            DB::table('group_chats')->updateOrInsert(
                [
                    'name' => $definition['name'],
                ],
                [
                    'created_by' => $definition['created_by'],
                    'created_at' => $groupTimestamp,
                    'updated_at' => $groupTimestamp,
                ],
            );

            $group = GroupChat::query()->where('name', $definition['name'])->firstOrFail();

            foreach ($definition['members'] as [$userId, $role, $lastReadAt]) {
                $memberTimestamp = $this->timestamp($lastReadAt);

                DB::table('group_members')->updateOrInsert(
                    [
                        'group_chat_id' => $group->id,
                        'user_id' => $userId,
                    ],
                    [
                        'role' => $role,
                        'last_read_at' => $memberTimestamp,
                        'created_at' => $memberTimestamp,
                        'updated_at' => $memberTimestamp,
                    ],
                );
            }

            foreach ($definition['messages'] as [$senderId, $body, $createdAt]) {
                $messageTimestamp = $this->timestamp($createdAt);

                DB::table('group_messages')->updateOrInsert(
                    [
                        'group_chat_id' => $group->id,
                        'created_at' => $messageTimestamp,
                    ],
                    [
                        'sender_id' => $senderId,
                        'body' => $body,
                        'updated_at' => $messageTimestamp,
                    ],
                );
            }
        }
    }

    /**
     * @param  array<string, User>  $users
     */
    private function cleanupLegacyDemoContent(array $users): void
    {
        $demoUserIds = collect($users)->pluck('id');

        $legacyPostIds = Post::query()
            ->where('user_id', $users['admin']->id)
            ->whereIn('body', self::LEGACY_POST_BODIES)
            ->pluck('id');

        if ($legacyPostIds->isNotEmpty()) {
            Like::query()->whereIn('post_id', $legacyPostIds)->delete();
            Post::query()->whereIn('id', $legacyPostIds)->delete();
        }

        Message::query()
            ->whereIn('body', self::LEGACY_DIRECT_MESSAGES)
            ->whereIn('sender_id', $demoUserIds)
            ->whereIn('receiver_id', $demoUserIds)
            ->delete();

        GroupChat::query()
            ->whereIn('name', self::LEGACY_GROUP_NAMES)
            ->delete();
    }

    private function at(int $year, int $month, int $day, int $hour, int $minute): CarbonImmutable
    {
        return CarbonImmutable::create($year, $month, $day, $hour, $minute, 0, config('app.timezone'));
    }

    private function timestamp(CarbonImmutable $value): string
    {
        return $value->format('Y-m-d H:i:s');
    }
}
