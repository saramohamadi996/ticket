<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
class TicketReplyNotification extends Notification
{
    use Queueable;

    protected $ticket;
    protected $reply;

    public function __construct($ticket, $reply)
    {
        $this->ticket = $ticket;
        $this->reply = $reply;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('پاسخ به تیکت')
            ->line('یک پاسخ جدید به تیکت شما ارسال شده است.')
            ->line('عنوان تیکت: ' . $this->ticket->title)
            ->line('محتوای پاسخ: ' . $this->reply)
            ->action('مشاهده تیکت', url('/tickets/' . $this->ticket->id))
            ->line('با تشکر از استفاده از سیستم تیکتینگ ما.');
    }

    public function toDatabase($notifiable)
    {
        return [
            'ticket_id' => $this->ticket->id,
            'message' => 'یک پاسخ جدید به تیکت شما ارسال شده است.',
            'url' => '/tickets/' . $this->ticket->id,
        ];
    }
}
