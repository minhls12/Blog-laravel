<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PostManagementTest extends TestCase
{
    use RefreshDatabase;

    private function validData(array $override = []): array
    {
        return array_merge([
            'title' => 'Bai viet moi',
            'body' => 'Noi dung bai viet du dai de hop le.',
            'category_id' => Category::factory()->create()->id,
            'status' => 'published',
        ], $override);
    }

    public function test_member_can_create_post_with_image_category_and_tags(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $tags = Tag::factory()->count(2)->create();

        $this->actingAs($user)->post(route('member.posts.store'), $this->validData([
            'tags' => $tags->pluck('id')->all(),
            'image' => UploadedFile::fake()->image('cover.jpg'),
        ]))->assertRedirect(route('member.posts.index'));

        $post = Post::firstWhere('title', 'Bai viet moi');
        $this->assertSame($user->id, $post->user_id);
        $this->assertSame('bai-viet-moi', $post->slug);
        $this->assertCount(2, $post->tags);
        Storage::disk('public')->assertExists($post->image);
    }

    public function test_create_fails_with_invalid_data(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('member.posts.store'), ['title' => '', 'body' => 'ngan', 'status' => 'abc'])
            ->assertSessionHasErrors(['title', 'body', 'category_id', 'status']);

        $this->assertDatabaseCount('posts', 0);
    }

    public function test_guest_cannot_create_post(): void
    {
        $this->post(route('member.posts.store'), $this->validData())->assertRedirect('/login');
        $this->assertDatabaseCount('posts', 0);
    }

    public function test_scheduled_post_needs_a_future_time(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('member.posts.store'), $this->validData(['status' => 'scheduled']))
            ->assertSessionHasErrors('published_at');
        $this->actingAs($user)->post(route('member.posts.store'), $this->validData([
            'status' => 'scheduled', 'published_at' => now()->subDay()->format('Y-m-d\TH:i'),
        ]))->assertSessionHasErrors('published_at');

        $this->actingAs($user)->post(route('member.posts.store'), $this->validData([
            'status' => 'scheduled', 'published_at' => now()->addDay()->format('Y-m-d\TH:i'),
        ]))->assertSessionHasNoErrors();

        $this->assertSame(Post::SCHEDULED, Post::first()->status);
    }

    public function test_author_can_update_own_post(): void
    {
        $post = Post::factory()->create();

        $this->actingAs($post->user)->put(route('member.posts.update', $post->id), $this->validData(['title' => 'Tieu de da sua', 'status' => 'draft']))
            ->assertRedirect(route('member.posts.index'));

        $this->assertSame('Tieu de da sua', $post->fresh()->title);
    }

    public function test_other_user_and_even_admin_cannot_edit_someone_elses_post_on_the_server(): void
    {
        $post = Post::factory()->create(['title' => 'Goc']);

        foreach ([User::factory()->create(), User::factory()->admin()->create()] as $intruder) {
            $this->actingAs($intruder)->get(route('member.posts.edit', $post->id))->assertForbidden();
            $this->actingAs($intruder)->put(route('member.posts.update', $post->id), $this->validData(['title' => 'Bi hack']))->assertForbidden();
            $this->actingAs($intruder)->delete(route('member.posts.destroy', $post->id))->assertForbidden();
        }

        $this->assertSame('Goc', $post->fresh()->title);
        $this->assertNotSoftDeleted($post);
    }

    public function test_delete_goes_to_trash_then_restore_then_force_delete(): void
    {
        $post = Post::factory()->published()->create();
        $this->actingAs($post->user);

        $this->delete(route('member.posts.destroy', $post->id))->assertRedirect();
        $this->assertSoftDeleted($post);
        $this->get(route('member.posts.trash'))->assertSee($post->title);

        $this->patch(route('member.posts.restore', $post->id))->assertRedirect();
        $this->assertNotSoftDeleted($post);

        $this->delete(route('member.posts.destroy', $post->id));
        $this->delete(route('member.posts.force', $post->id))->assertRedirect();
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    public function test_other_user_cannot_restore_or_force_delete(): void
    {
        $post = Post::factory()->create();
        $post->delete();
        $other = User::factory()->create();

        $this->actingAs($other)->patch(route('member.posts.restore', $post->id))->assertForbidden();
        $this->actingAs($other)->delete(route('member.posts.force', $post->id))->assertForbidden();

        $this->assertSoftDeleted($post);
    }
}
