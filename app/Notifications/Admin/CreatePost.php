<?php

namespace App\Notifications\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CreatePost extends Notification implements ShouldQueue
{
    use Queueable;

    private $post;
    private $worker;
    private $content = "Create new Post";

    /**
     * Create a new notification instance.
     */
    public function __construct($post , $worker)
    {
        $this->worker = $worker;
        $this->post = $post;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            "worker" => $this->worker,
            "post" => $this->post,
            "content" => $this->content,
        ];
    }
}
