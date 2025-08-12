<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminAnnouncement extends Notification
{
    use Queueable;

    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function via($notifiable)
    {
        $channels = ['database'];
        if ($this->data['send_email'] ?? false) {
            $channels[] = 'mail';
        }
        return $channels;
    }
    
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject($this->data['title'])
                    ->view('emails.notification', ['data' => $this->data]);
    }

    public function toArray($notifiable)
    {
        return $this->toDatabase($notifiable);
    }
    
    public function toDatabase($notifiable)
    {
        return [
            'title' => $this->data['title'],
            'message' => strip_tags($this->data['message']),
            'data' => [
                'title' => $this->data['title'],
                'message' => $this->data['message'],
                'type' => $this->data['type'],
                'sender' => $this->data['sender'],
                'sender_role' => $this->data['sender_role'] ?? 'Admin'
            ]
        ];
    }
}