<?php

namespace App\Mail;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewCommentMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Comment $comment)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bài viết "'.$this->comment->post->title.'" có bình luận mới',
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.new-comment');
    }
}
