<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PublishScheduledPosts extends Command
{
    protected $signature = 'posts:publish-scheduled';

    protected $description = 'Tự động đăng các bài hẹn giờ đã đến hạn';

    public function handle(): int
    {
        $count = 0;

        Post::where('status', Post::SCHEDULED)
            ->where('published_at', '<=', now())
            ->get()
            ->each(function (Post $post) use (&$count) {
                $post->update(['status' => Post::PUBLISHED]);
                Log::info('Tự động đăng bài hẹn giờ', ['post_id' => $post->id]);
                $count++;
            });

        $this->info("Đã đăng {$count} bài viết.");

        return self::SUCCESS;
    }
}
