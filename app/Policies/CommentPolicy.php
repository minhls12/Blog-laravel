<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{
    // Người viết bình luận, tác giả bài viết và quản trị viên được xóa.
    public function delete(User $user, Comment $comment): bool
    {
        return (int) $user->id === (int) $comment->user_id
            || (int) $user->id === (int) $comment->post?->user_id
            || (bool) $user->is_admin;
    }
}
