<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_sees_login_screen(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Вход в систему');
    }

    public function test_first_admin_can_be_bootstrapped(): void
    {
        $response = $this->post('/setup-admin', [
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'admin@example.com',
            'role' => 'admin',
        ]);
    }

    public function test_admin_can_create_post_and_employee_can_like_and_comment(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'password' => Hash::make('secret123'),
        ]);

        $employee = User::factory()->create();

        $this->actingAs($admin)->post(route('posts.store'), [
            'body' => 'Quarterly town hall announcement.',
        ])->assertRedirect(route('dashboard'));

        $post = Post::query()->firstOrFail();

        $this->actingAs($employee)
            ->postJson(route('posts.like', $post))
            ->assertOk()
            ->assertJsonPath('likes_count', 1);

        $this->actingAs($employee)
            ->postJson(route('posts.comments.store', $post), [
                'body' => 'Got it, thanks.',
            ])
            ->assertOk()
            ->assertJsonPath('comments_count', 1);

        $this->assertDatabaseHas('comments', [
            'post_id' => $post->id,
            'user_id' => $employee->id,
            'body' => 'Got it, thanks.',
        ]);
    }

    public function test_employee_can_send_direct_message(): void
    {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();

        $this->actingAs($sender)
            ->postJson(route('messages.direct.store'), [
                'receiver_id' => $receiver->id,
                'body' => 'Hello from Laravel chat.',
            ])
            ->assertOk()
            ->assertJsonPath('message.body', 'Hello from Laravel chat.');

        $this->assertDatabaseHas('messages', [
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'body' => 'Hello from Laravel chat.',
        ]);
    }

    public function test_non_admin_cannot_access_admin_dashboard(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }
}
