<?php

namespace Tests\Feature;

use App\Mail\NewCommentMail;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_can_comment_and_author_gets_queued_email(): void
    {
        Mail::fake();
        $post = Post::factory()->published()->create();
        $member = User::factory()->create();

        $this->actingAs($member)->post(route('comments.store', $post->slug), ['body' => 'Bai hay qua'])->assertRedirect();

        $this->assertDatabaseHas('comments', ['post_id' => $post->id, 'user_id' => $member->id, 'body' => 'Bai hay qua']);
        Mail::assertQueued(NewCommentMail::class, fn ($mail) => $mail->hasTo($post->user->email));
    }

    public function test_guest_cannot_comment(): void
    {
        Mail::fake();
        $post = Post::factory()->published()->create();

        $this->post(route('comments.store', $post->slug), ['body' => 'Spam'])->assertRedirect('/login');

        $this->assertDatabaseCount('comments', 0);
        Mail::assertNothingQueued();
    }

    public function test_empty_comment_or_comment_on_draft_is_rejected(): void
    {
        Mail::fake();
        $member = User::factory()->create();
        $published = Post::factory()->published()->create();
        $draft = Post::factory()->create();

        $this->actingAs($member)->post(route('comments.store', $published->slug), ['body' => ''])->assertSessionHasErrors('body');
        $this->actingAs($member)->post(route('comments.store', $draft->slug), ['body' => 'Thu'])->assertNotFound();

        $this->assertDatabaseCount('comments', 0);
    }

    public function test_commenter_post_author_and_admin_can_delete_comment(): void
    {
        $post = Post::factory()->published()->create();
        $commenter = User::factory()->create();
        $admin = User::factory()->admin()->create();

        foreach ([$commenter, $post->user, $admin] as $actor) {
            $comment = Comment::factory()->create(['post_id' => $post->id, 'user_id' => $commenter->id]);
            $this->actingAs($actor)->delete(route('comments.destroy', $comment->id))->assertRedirect();
            $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
        }
    }

    public function test_unrelated_member_cannot_delete_comment(): void
    {
        $comment = Comment::factory()->create();

        $this->actingAs(User::factory()->create())->delete(route('comments.destroy', $comment->id))->assertForbidden();

        $this->assertDatabaseHas('comments', ['id' => $comment->id]);
    }
}
