<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Complaint;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class NewComplaintNotification extends Notification
{
    use Queueable;

    public function __construct(public Complaint $complaint) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Complaint Submitted: '.$this->complaint->title)
            ->line('A new complaint has been submitted.')
            ->line('**Title:** '.$this->complaint->title)
            ->line('**Customer:** '.$this->complaint->customer->name.' ('.$this->complaint->customer->email.')')
            ->line('**Description:**')
            ->line($this->complaint->description)
            ->action('View Complaint', url('/admin/complaints/'.$this->complaint->id.'/edit'))
            ->line('Please review, set severity, and assign this complaint as soon as possible.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'complaint_id' => $this->complaint->id,
            'title' => $this->complaint->title,
            'severity' => $this->complaint->severity,
            'customer_name' => $this->complaint->customer->name,
            'customer_email' => $this->complaint->customer->email,
        ];
    }
}
