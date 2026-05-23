<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Messages\MailMessage;

class PostRequestedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $studentRequest;
    public $post;

    public function __construct($studentRequest, $post = null)
    {
        $this->studentRequest = $studentRequest;
        $this->post = $post;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'post_requested',
            'student_request_id' => $this->studentRequest->getKey(),
            'student_name' => $this->studentRequest->student_name ?? null,
            'post_id' => $this->studentRequest->post_id ?? null,
            'message' => $this->studentRequest->description ?? null,
            'url' => $this->post ? route('teacher.posts.requests', $this->post->getKey()) : null,
        ];
    }

    public function toMail($notifiable)
    {
        $mail = (new MailMessage)
            ->subject('New request for your post on TutorLink BD')
            ->greeting('Hello,')
            ->line($this->studentRequest->student_name . ' has requested your post.')
            ->line($this->studentRequest->description ?? '')
            ->action('View request', url('/'))
            ->line('Thank you for using TutorLink BD.');

        return $mail;
    }

}
