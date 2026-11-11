<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'title' => fake()->unique()->sentence(6),
            'body' => implode("\n\n", fake()->paragraphs(4)),
            'status' => Post::DRAFT,
            'published_at' => null,
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => [
            'status' => Post::PUBLISHED,
            'published_at' => fake()->dateTimeBetween('-60 days', '-1 minute'),
        ]);
    }

    public function scheduled(): static
    {
        return $this->state(fn () => [
            'status' => Post::SCHEDULED,
            'published_at' => fake()->dateTimeBetween('+1 day', '+30 days'),
        ]);
    }
}
