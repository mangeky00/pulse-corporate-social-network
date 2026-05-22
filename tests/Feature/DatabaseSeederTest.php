<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\DemoNetworkSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_is_idempotent_and_creates_known_accounts(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@socnetwork.local')->first();

        $this->assertNotNull($admin);
        $this->assertSame('admin', $admin->role);
        $this->assertTrue(Hash::check(DemoNetworkSeeder::DEFAULT_PASSWORD, $admin->password));

        $this->assertDatabaseCount('users', 6);
        $this->assertDatabaseCount('posts', 3);
        $this->assertDatabaseCount('likes', 8);
        $this->assertDatabaseCount('comments', 6);
        $this->assertDatabaseCount('messages', 7);
        $this->assertDatabaseCount('group_chats', 2);
        $this->assertDatabaseCount('group_members', 7);
        $this->assertDatabaseCount('group_messages', 5);

        $this->assertDatabaseHas('users', [
            'email' => 'olivia.brooks@socnetwork.local',
            'department' => 'Продукт',
        ]);

        $this->assertDatabaseHas('group_chats', [
            'name' => 'Запуск портала',
        ]);
    }
}
