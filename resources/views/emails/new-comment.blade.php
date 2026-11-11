<!doctype html>
<html lang="vi">
<body style="font-family:Arial,sans-serif">
    <p>Xin chào {{ $comment->post->user->name }},</p>
    <p><strong>{{ $comment->user->name }}</strong> vừa bình luận vào bài viết
       <a href="{{ route('posts.show', $comment->post->slug) }}">{{ $comment->post->title }}</a>:</p>
    <blockquote style="border-left:3px solid #ccc;padding-left:10px">{{ $comment->body }}</blockquote>
</body>
</html>
