<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_sees_dashboard_with_statistics(): void
    {
        $admin = User::factory()->admin()->create();
        $post = Post::factory()->published()->create(['title' => 'Bai hot']);
        Comment::factory()->count(3)->create(['post_id' => $post->id]);

        $this->actingAs($admin)->get('/admin')
            ->assertOk()
            ->assertViewHas('commentCount', 3)
            ->assertViewHas('postCount', 1)
            ->assertSee('Bai hot');
    }

    public function test_member_gets_403_from_admin_area(): void
    {
        $this->actingAs(User::factory()->create())->get('/admin')->assertForbidden();
        $this->actingAs(User::factory()->create())->get('/admin/categories')->assertForbidden();
    }

    public function test_guest_is_redirected_to_login_from_admin_area(): void
    {
        $this->get('/admin')->assertRedirect('/login');
    }

    public function test_admin_can_create_update_and_delete_category(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post('/admin/categories', ['name' => 'Cong nghe'])->assertRedirect('/admin/categories');
        $category = Category::firstWhere('name', 'Cong nghe');
        $this->assertSame('cong-nghe', $category->slug);

        $this->actingAs($admin)->put('/admin/categories/'.$category->id, ['name' => 'Cong nghe moi']);
        $this->assertSame('Cong nghe moi', $category->fresh()->name);

        $this->actingAs($admin)->delete('/admin/categories/'.$category->id);
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_category_validation_fails_for_empty_or_duplicate_name(): void
    {
        $admin = User::factory()->admin()->create();
        Category::factory()->create(['name' => 'Doi song']);

        $this->actingAs($admin)->post('/admin/categories', ['name' => ''])->assertSessionHasErrors('name');
        $this->actingAs($admin)->post('/admin/categories', ['name' => 'Doi song'])->assertSessionHasErrors('name');
        $this->assertDatabaseCount('categories', 1);
    }

    public function test_member_cannot_manage_categories(): void
    {
        $this->actingAs(User::factory()->create())->post('/admin/categories', ['name' => 'Hack'])->assertForbidden();
        $this->assertDatabaseCount('categories', 0);
    }

    public function test_admin_can_delete_any_post_and_comment(): void
    {
        $admin = User::factory()->admin()->create();
        $post = Post::factory()->published()->create();
        $comment = Comment::factory()->create(['post_id' => $post->id]);

        $this->actingAs($admin)->get('/admin/posts')->assertOk()->assertSee($post->title);
        $this->actingAs($admin)->delete(route('comments.destroy', $comment->id))->assertRedirect();
        $this->actingAs($admin)->delete(route('admin.posts.destroy', $post->id))->assertRedirect('/admin/posts');

        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    public function test_member_cannot_use_admin_post_delete(): void
    {
        $post = Post::factory()->published()->create();

        $this->actingAs($post->user)->delete(route('admin.posts.destroy', $post->id))->assertForbidden();
        $this->assertDatabaseHas('posts', ['id' => $post->id]);
    }
}
