<?php

namespace Tests\Feature;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScheduledPostsTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_publishes_due_scheduled_posts(): void
    {
        $due = Post::factory()->create(['status' => 'scheduled', 'published_at' => now()->subMinute()]);

        $this->artisan('posts:publish-scheduled')->expectsOutput('Đã đăng 1 bài viết.')->assertSuccessful();

        $this->assertSame(Post::PUBLISHED, $due->fresh()->status);
        $this->get('/')->assertSee($due->title);
    }

    public function test_command_leaves_future_and_draft_posts_untouched(): void
    {
        $future = Post::factory()->scheduled()->create();
        $draft = Post::factory()->create();

        $this->artisan('posts:publish-scheduled')->expectsOutput('Đã đăng 0 bài viết.')->assertSuccessful();

        $this->assertSame(Post::SCHEDULED, $future->fresh()->status);
        $this->assertSame(Post::DRAFT, $draft->fresh()->status);
    }

    public function test_command_is_registered_in_the_scheduler(): void
    {
        $this->artisan('schedule:list')->expectsOutputToContain('posts:publish-scheduled')->assertSuccessful();
    }
}
