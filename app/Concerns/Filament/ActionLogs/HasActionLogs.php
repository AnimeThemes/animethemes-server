<?php

declare(strict_types=1);

namespace App\Concerns\Filament\ActionLogs;

use App\Models\Admin\ActionLog;
use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;
use Throwable;

trait HasActionLogs
{
    protected string $batchId = '';
    protected ?ActionLog $actionLog = null;
    protected ?Model $recordLog = null;
    protected ?Model $parentRecordLog = null;
    protected ?Model $pivot = null;

    public function createBatchId(): string
    {
        $this->batchId = Str::orderedUuid()->__toString();

        return $this->batchId;
    }

    public function createActionLog(Action $action, Model $record, ?bool $shouldCreateNewBatchId = true): void
    {
        if ($shouldCreateNewBatchId) {
            $this->createBatchId();
        }

        $this->recordLog = $record;

        $actionLog = ActionLog::modelActioned(
            $this->batchId,
            $action,
            $this->recordLog,
        );

        $this->actionLog = $actionLog;
    }

    public function updateLog(Model $relatedModel, Model $pivot): void
    {
        $this->actionLog->update([
            ActionLog::ATTRIBUTE_TARGET_TYPE => Relation::getMorphAlias($relatedModel->getMorphClass()),
            ActionLog::ATTRIBUTE_TARGET_ID => $relatedModel->getKey(),
            ActionLog::ATTRIBUTE_MODEL_TYPE => Relation::getMorphAlias($pivot->getMorphClass()),
            ActionLog::ATTRIBUTE_MODEL_ID => $pivot->getKey(),
        ]);
    }

    public function failedLog(Throwable|string|null $exception): void
    {
        $this->actionLog->failed($exception);

        // Filament notifications
        if ($this instanceof Action) {
            $this->failureNotificationTitle(Str::limit($exception, 200));
            $this->failure();
        }
    }

    public function finishedLog(): void
    {
        if (($actionLog = $this->actionLog) && ! $this->hasFailedLog()) {
            $actionLog->finished();
            // Filament notifications
            if ($this instanceof Action) {
                $this->success();
            }
        }
    }

    public function batchFinishedLog(): void
    {
        $this->actionLog->batchFinished();
    }

    public function hasFailedLog(): bool
    {
        return $this->actionLog->hasFailed();
    }
}
