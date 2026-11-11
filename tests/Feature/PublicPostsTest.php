<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPostsTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_lists_only_published_posts_newest_first(): void
    {
        Post::factory()->published()->create(['title' => 'Bai cu', 'published_at' => now()->subDays(2)]);
        Post::factory()->published()->create(['title' => 'Bai moi', 'published_at' => now()->subDay()]);
        Post::factory()->create(['title' => 'Bai nhap']);
        Post::factory()->scheduled()->create(['title' => 'Bai hen gio']);

        $this->get('/')
            ->assertOk()
            ->assertSeeInOrder(['Bai moi', 'Bai cu'])
            ->assertDontSee('Bai nhap')
            ->assertDontSee('Bai hen gio');
    }

    public function test_home_is_paginated(): void
    {
        Post::factory()->count(12)->published()->create();

        $this->get('/')->assertOk()->assertViewHas('posts', fn ($p) => $p->count() === 9 && $p->total() === 12);
    }

    public function test_search_finds_matching_posts_and_returns_nothing_for_unknown_keyword(): void
    {
        Post::factory()->published()->create(['title' => 'Hoc Laravel co ban']);
        Post::factory()->published()->create(['title' => 'Nau an ngon']);

        $this->get('/?q=Laravel')->assertSee('Hoc Laravel co ban')->assertDontSee('Nau an ngon');
        $this->get('/?q=khongcotuokhoanay')->assertOk()->assertSee('Chưa có bài viết nào phù hợp');
    }

    public function test_filter_by_category_and_tag(): void
    {
        $tech = Category::factory()->create(['name' => 'Cong nghe']);
        $life = Category::factory()->create(['name' => 'Doi song']);
        $tag = Tag::factory()->create(['name' => 'PHP']);

        $a = Post::factory()->published()->create(['title' => 'Bai cong nghe', 'category_id' => $tech->id]);
        Post::factory()->published()->create(['title' => 'Bai doi song', 'category_id' => $life->id]);
        $a->tags()->attach($tag);

        $this->get('/?category='.$tech->slug)->assertSee('Bai cong nghe')->assertDontSee('Bai doi song');
        $this->get('/?tag='.$tag->slug)->assertSee('Bai cong nghe')->assertDontSee('Bai doi song');
    }

    public function test_published_post_detail_is_accessible_by_slug_and_shows_comments(): void
    {
        $post = Post::factory()->published()->create(['title' => 'Bai chi tiet']);
        $post->comments()->create(['user_id' => User::factory()->create()->id, 'body' => 'Binh luan hay']);

        $this->get('/posts/'.$post->slug)->assertOk()->assertSee('Bai chi tiet')->assertSee('Binh luan hay');
    }

    public function test_guest_cannot_see_draft_but_author_can(): void
    {
        $post = Post::factory()->create(['title' => 'Ban nhap']);

        $this->get('/posts/'.$post->slug)->assertNotFound();
        $this->actingAs($post->user)->get('/posts/'.$post->slug)->assertOk()->assertSee('Ban nhap');
    }
}
