<?php

declare(strict_types=1);

namespace App\Concerns\Filament;

use App\Enums\Models\Admin\ActivityStatus;
use App\Models\Admin\Activity;
use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Throwable;

trait HasActivityLogs
{
    protected ?Activity $activity = null;

    public function createActivityLog(Action $action, Model $record): void
    {
        /** @phpstan-ignore-next-line */
        $this->activity = activity()
            ->performedOn($record)
            ->withProperties($action->getData())
            ->tap(function (Activity $activity): void {
                $activity->status = ActivityStatus::RUNNING;
            })
            ->event($action->getLabel())
            ->log('actioned');
    }

    public function failedLog(Throwable|string|null $exception): void
    {
        $this->activity?->update([
            Activity::ATTRIBUTE_STATUS => ActivityStatus::FAILED,
            Activity::ATTRIBUTE_EXCEPTION => Str::limit($exception, 200),
            Activity::ATTRIBUTE_FINISHED_AT => now(),
        ]);

        // Filament notifications
        if ($this instanceof Action) {
            $this->failureNotificationTitle(Str::limit($exception, 200));
            $this->failure();
        }
    }

    public function finishedLog(): void
    {
        if (! $this->hasFailedLog()) {
            $this->activity?->update([
                Activity::ATTRIBUTE_STATUS => ActivityStatus::FINISHED,
                Activity::ATTRIBUTE_FINISHED_AT => now(),
            ]);

            // Filament notifications
            if ($this instanceof Action) {
                $this->success();
            }
        }
    }

    public function hasFailedLog(): bool
    {
        return $this->activity?->status === ActivityStatus::FAILED;
    }
}
