<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    private function owns(User $user, Post $post): bool
    {
        return (int) $user->id === (int) $post->user_id;
    }

    // Chỉ tác giả mới được sửa / xóa / khôi phục bài của mình.
    // Quản trị viên xóa bài vi phạm qua khu vực /admin, không sửa bài người khác.
    public function update(User $user, Post $post): bool
    {
        return $this->owns($user, $post);
    }

    public function delete(User $user, Post $post): bool
    {
        return $this->owns($user, $post);
    }

    public function restore(User $user, Post $post): bool
    {
        return $this->owns($user, $post);
    }

    public function forceDelete(User $user, Post $post): bool
    {
        return $this->owns($user, $post);
    }
}
