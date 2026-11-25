<?php

namespace App\Console\Commands;

use App\Enums\ReadingPlanStatus;
use App\Models\ReadingPlan;
use App\Notifications\ReadingPlanReminderNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendReadingPlanRemindersCommand extends Command
{
    protected $signature = 'reading-plans:remind';

    protected $description = '読書計画の期限前・当日・期限超過リマインダー通知を作成し、必要に応じてステータスを更新します。';

    public function handle(): int
    {
        $today = Carbon::today('Asia/Tokyo');
        $approachingDate = $today->copy()->addDays(3); 

        $plans = ReadingPlan::with(['user', 'book'])
            ->where('status', '!=', ReadingPlanStatus::Completed)
            ->get();

        $notifiedCount = 0;

        foreach ($plans as $plan) {
            if (! $plan->user || ! $plan->target_date) {
                continue;
            }

            $targetDate = Carbon::parse($plan->target_date, )
            ->timezone('Asia/Tokyo')
            ->startOfDay();
            
            $bookTitle = $plan->book?->title ?? '対象書籍';

            if ($targetDate->lt($today)) {
                if ($plan->status !== ReadingPlanStatus::Overdue->value) {
                    $plan->update([
                        'status' => ReadingPlanStatus::Overdue->value,
                    ]);
                }

                $alreadyNotified = $plan->user->notifications()
                    ->whereDate('created_at', $today)
                    ->where('data->reading_plan_id', $plan->id)
                    ->where('data->reminder_type', 'overdue')
                    ->exists();

                if (! $alreadyNotified) {
                    $plan->user->notify(new ReadingPlanReminderNotification(
                        $plan,
                        'overdue',
                        "「{$bookTitle}」の読書計画の期日（{$targetDate->toDateString()}）を過ぎています。"
                    ));
                    $notifiedCount++;
                }
            } elseif ($targetDate->eq($today)) {
                $alreadyNotified = $plan->user->notifications()
                    ->whereDate('created_at', $today)
                    ->where('data->reading_plan_id', $plan->id)
                    ->where('data->reminder_type', 'today')
                    ->exists();

                if (! $alreadyNotified) {
                    $plan->user->notify(new ReadingPlanReminderNotification(
                        $plan,
                        'today',
                        "本日（{$targetDate->toDateString()}）は「{$bookTitle}」の読書計画の期日です。"
                    ));
                    $notifiedCount++;
                }
            } elseif ($targetDate->lte($approachingDate)) {
                $alreadyNotified = $plan->user->notifications()
                    ->whereDate('created_at', $today)
                    ->where('data->reading_plan_id', $plan->id)
                    ->where('data->reminder_type', 'approaching')
                    ->exists();

                if (! $alreadyNotified) {
                    $plan->user->notify(new ReadingPlanReminderNotification(
                        $plan,
                        'approaching',
                        "「{$bookTitle}」の読書計画の期日（{$targetDate->toDateString()}）が近づいています。"
                    ));
                    $notifiedCount++;
                }
            }
        }

        $this->info("リマインダー通知の作成が完了しました（送信件数: {$notifiedCount}件）。");

        return self::SUCCESS;
    }
}