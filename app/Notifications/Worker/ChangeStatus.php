<?php

namespace App\Notifications\Worker;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ChangeStatus extends Notification
{
    use Queueable;


    protected $content = "Change Status Post";
    /**
     * Create a new notification instance.
     */
    public function __construct(protected $post)
    {

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
            "content" => $this->content,
            "post" => $this->post,
        ];
    }
}
