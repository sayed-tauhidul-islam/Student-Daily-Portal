<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PostRequested extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $post;
    public $student;
    public $requestModel;

    /**
     * Create a new message instance.
     */
    public function __construct($post, $student, $requestModel)
    {
        $this->post = $post;
        $this->student = $student;
        $this->requestModel = $requestModel;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('New request for your post on TutorLink BD')
            ->view('emails.post_requested');
    }
}
