<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register(): void
    {
        $this->post('/register', [
            'name' => 'Minh', 'email' => 'minh@example.com',
            'password' => 'matkhau123', 'password_confirmation' => 'matkhau123',
        ])->assertRedirect('/');

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'minh@example.com', 'is_admin' => false]);
    }

    public function test_registration_fails_with_duplicate_email_or_bad_password(): void
    {
        User::factory()->create(['email' => 'minh@example.com']);

        $this->post('/register', [
            'name' => 'A', 'email' => 'minh@example.com',
            'password' => '123', 'password_confirmation' => 'khac',
        ])->assertSessionHasErrors(['email', 'password']);

        $this->assertGuest();
    }

    public function test_registration_cannot_make_user_admin(): void
    {
        $this->post('/register', [
            'name' => 'Hacker', 'email' => 'h@example.com', 'is_admin' => 1,
            'password' => 'matkhau123', 'password_confirmation' => 'matkhau123',
        ]);

        $this->assertFalse(User::where('email', 'h@example.com')->first()->is_admin);
    }

    public function test_user_can_login_and_logout(): void
    {
        $user = User::factory()->create(['email' => 'a@example.com']);

        $this->post('/login', ['email' => 'a@example.com', 'password' => 'password'])->assertRedirect('/');
        $this->assertAuthenticatedAs($user);

        $this->post('/logout')->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_login_fails_with_wrong_password(): void
    {
        User::factory()->create(['email' => 'a@example.com']);

        $this->from('/login')->post('/login', ['email' => 'a@example.com', 'password' => 'sai'])
            ->assertRedirect('/login')
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}
