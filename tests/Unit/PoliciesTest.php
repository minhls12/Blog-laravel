<?php

namespace Tests\Unit;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\Policies\CommentPolicy;
use App\Policies\PostPolicy;
use Tests\TestCase;

class PoliciesTest extends TestCase
{
    private function user(int $id, bool $admin = false): User
    {
        $user = new User();
        $user->id = $id;
        $user->is_admin = $admin;

        return $user;
    }

    public function test_only_the_owner_passes_post_policy(): void
    {
        $post = new Post();
        $post->user_id = 1;
        $policy = new PostPolicy();

        $this->assertTrue($policy->update($this->user(1), $post));
        $this->assertTrue($policy->restore($this->user(1), $post));
        $this->assertFalse($policy->update($this->user(2), $post));
        $this->assertFalse($policy->forceDelete($this->user(2, admin: true), $post));
    }

    public function test_comment_policy_allows_owner_post_author_and_admin_only(): void
    {
        $post = new Post();
        $post->user_id = 10;
        $comment = new Comment();
        $comment->user_id = 20;
        $comment->setRelation('post', $post);
        $policy = new CommentPolicy();

        $this->assertTrue($policy->delete($this->user(20), $comment));
        $this->assertTrue($policy->delete($this->user(10), $comment));
        $this->assertTrue($policy->delete($this->user(99, admin: true), $comment));
        $this->assertFalse($policy->delete($this->user(99), $comment));
    }
}
