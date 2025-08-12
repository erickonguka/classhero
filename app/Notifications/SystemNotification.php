<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SystemNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }
    
    public function toMail($notifiable)
    {
        $template = $this->getEmailTemplate();
        
        return (new MailMessage)
                    ->subject($this->data['title'])
                    ->view($template, ['data' => $this->data]);
    }
    
    private function getEmailTemplate()
    {
        $type = $this->data['type'] ?? 'default';
        
        $templates = [
            'comment' => 'emails.types.comment',
            'certificate_approved' => 'emails.types.certificate',
        ];
        
        return $templates[$type] ?? 'emails.notification';
    }

    public function toArray($notifiable)
    {
        return [
            'title' => $this->data['title'],
            'message' => strip_tags($this->data['message']),
            'data' => $this->data
        ];
    }
}