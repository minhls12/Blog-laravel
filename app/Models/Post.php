<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    public const DRAFT = 'draft';
    public const PUBLISHED = 'published';
    public const SCHEDULED = 'scheduled';

    public const STATUSES = [
        self::DRAFT => 'Nháp',
        self::PUBLISHED => 'Đã đăng',
        self::SCHEDULED => 'Hẹn giờ đăng',
    ];

    protected $fillable = ['category_id', 'title', 'slug', 'body', 'image', 'status', 'published_at'];

    protected function casts(): array
    {
        return ['published_at' => 'datetime'];
    }

    protected static function booted(): void
    {
        static::creating(function (Post $post) {
            if (empty($post->slug)) {
                $post->slug = static::uniqueSlug((string) $post->title);
            }
        });

        // Xóa vĩnh viễn thì xóa luôn file ảnh.
        static::forceDeleting(function (Post $post) {
            if ($post->image) {
                Storage::disk('public')->delete($post->image);
            }
        });
    }

    public static function uniqueSlug(string $title): string
    {
        $base = Str::slug($title) ?: 'bai-viet';
        $slug = $base;
        $i = 2;

        while (static::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }

    public function isDraft(): bool
    {
        return $this->status === self::DRAFT;
    }

    public function isPublished(): bool
    {
        return $this->status === self::PUBLISHED;
    }

    public function isScheduled(): bool
    {
        return $this->status === self::SCHEDULED;
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', self::PUBLISHED)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        $term = trim((string) $term);
        if ($term === '') {
            return $query;
        }

        $like = '%'.addcslashes($term, '%_\\').'%';

        return $query->where(function (Builder $q) use ($like) {
            $q->where('title', 'like', $like)->orWhere('body', 'like', $like);
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->latest();
    }
}
