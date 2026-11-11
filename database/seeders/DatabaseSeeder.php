<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Tài khoản admin: admin@example.com / password
        User::factory()->admin()->create(['name' => 'Quản trị viên', 'email' => 'admin@example.com']);

        // Thành viên: member@example.com / password và 4 thành viên ngẫu nhiên
        $members = User::factory(4)->create()
            ->push(User::factory()->create(['name' => 'Thành viên mẫu', 'email' => 'member@example.com']));

        $categories = collect(['Công nghệ', 'Đời sống', 'Học tập', 'Du lịch', 'Ẩm thực'])
            ->map(fn ($name) => Category::create(['name' => $name]));

        $tags = collect(['Laravel', 'PHP', 'MySQL', 'Mẹo hay', 'Review', 'Tin tức', 'Hướng dẫn', 'Chia sẻ'])
            ->map(fn ($name) => Tag::create(['name' => $name]));

        $randomOwner = fn () => [
            'user_id' => $members->random()->id,
            'category_id' => $categories->random()->id,
        ];

        $published = Post::factory(20)->published()->state($randomOwner)->create();
        $others = Post::factory(4)->state($randomOwner)->create()
            ->merge(Post::factory(3)->scheduled()->state($randomOwner)->create());

        $published->merge($others)->each(
            fn (Post $post) => $post->tags()->attach($tags->random(rand(1, 3))->pluck('id'))
        );

        Comment::factory(40)->state(fn () => [
            'post_id' => $published->random()->id,
            'user_id' => $members->random()->id,
        ])->create();
    }
}
