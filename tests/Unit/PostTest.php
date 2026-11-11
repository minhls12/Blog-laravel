<?php

namespace Tests\Unit;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    public function test_status_helpers_return_true_for_matching_status_only(): void
    {
        $draft = new Post(['status' => Post::DRAFT]);
        $published = new Post(['status' => Post::PUBLISHED]);
        $scheduled = new Post(['status' => Post::SCHEDULED]);

        $this->assertTrue($draft->isDraft());
        $this->assertTrue($published->isPublished());
        $this->assertTrue($scheduled->isScheduled());

        $this->assertFalse($draft->isPublished());
        $this->assertFalse($published->isScheduled());
        $this->assertFalse($scheduled->isDraft());
    }

    public function test_slug_is_generated_from_vietnamese_title_and_stays_unique(): void
    {
        $first = Post::factory()->create(['title' => 'Học Laravel cơ bản']);
        $second = Post::factory()->create(['title' => 'Học Laravel cơ bản']);

        $this->assertSame('hoc-laravel-co-ban', $first->slug);
        $this->assertSame('hoc-laravel-co-ban-2', $second->slug);
        $this->assertNotSame($first->slug, $second->slug);
    }
}
