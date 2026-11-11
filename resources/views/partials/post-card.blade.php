<div class="card h-100 shadow-sm">
    @if($post->image)
        <img src="{{ asset('storage/'.$post->image) }}" class="card-img-top post-cover" alt="{{ $post->title }}">
    @endif
    <div class="card-body d-flex flex-column">
        @if($post->category)
            <a href="{{ route('home', ['category' => $post->category->slug]) }}" class="badge text-bg-primary text-decoration-none align-self-start mb-2">{{ $post->category->name }}</a>
        @endif
        <h2 class="h5"><a href="{{ route('posts.show', $post->slug) }}" class="text-decoration-none text-dark">{{ $post->title }}</a></h2>
        <p class="text-muted small mb-2">{{ $post->user->name }} · {{ $post->published_at?->format('d/m/Y') }}</p>
        <p class="card-text">{{ \Illuminate\Support\Str::limit(strip_tags($post->body), 120) }}</p>
        <div class="mt-auto">
            @foreach($post->tags as $tag)
                <a href="{{ route('home', ['tag' => $tag->slug]) }}" class="badge text-bg-light border text-decoration-none">#{{ $tag->name }}</a>
            @endforeach
        </div>
    </div>
</div>
