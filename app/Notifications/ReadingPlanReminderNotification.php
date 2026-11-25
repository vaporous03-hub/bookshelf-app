<?php

namespace App\Notifications;

use App\Models\ReadingPlan;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReadingPlanReminderNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly ReadingPlan $readingPlan,
        public readonly string $reminderType, // 'approaching' | 'today' | 'overdue'
        public readonly string $message
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'reading_plan_id' => $this->readingPlan->id,
            'book_id' => $this->readingPlan->book_id,
            'book_title' => $this->readingPlan->book?->title,
            'target_date' => $this->readingPlan->target_date,
            'reminder_type' => $this->reminderType,
            'message' => $this->message,
        ];
    }
}