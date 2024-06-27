<?php

declare(strict_types=1);

namespace App\Concerns\Filament\Actions;

use App\Models\Admin\ActionLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Throwable;

/**
 * Trait HasActionLogs.
 */
trait HasActionLogs
{
    protected string $batchId = '';
    protected ?ActionLog $actionLog = null;
    protected ?Model $recordLog = null;
    protected ?Model $parentRecordLog = null;
    protected ?Model $pivot = null;

    /**
     * Create a batch id for the action.
     *
     * @return string
     */
    public function createBatchId(): string
    {
        $this->batchId = Str::orderedUuid()->__toString();

        return $this->batchId;
    }

    /**
     * Create an action log.
     *
     * @param  mixed  $action
     * @param  Model|null  $record
     * @param  bool  $shouldCreateNewBatchId
     * @return void
     */
    public function createActionLog(mixed $action, ?Model $record = null, ?bool $shouldCreateNewBatchId = true): void
    {
        if ($shouldCreateNewBatchId) {
            $this->createBatchId();
        }
        
        // $record must be specified if in context of bulk action
        $this->recordLog = $record ?? $this->getRecord();

        $actionLog = ActionLog::modelActioned(
            $this->batchId,
            $action,
            $this->recordLog,
        );

        $this->actionLog = $actionLog;
    }

    /**
     * Update the log for pivot actions.
     *
     * @param  Model  $relatedModel
     * @param  Model  $pivot
     * @return void
     */
    public function updateLog(Model $relatedModel, Model $pivot): void
    {
        $this->actionLog->update([
            ActionLog::ATTRIBUTE_TARGET_TYPE => $relatedModel->getMorphClass(),
            ActionLog::ATTRIBUTE_TARGET_ID => $relatedModel->getKey(),
            ActionLog::ATTRIBUTE_MODEL_TYPE => $pivot->getMorphClass(),
            ActionLog::ATTRIBUTE_MODEL_ID => $pivot->getKey(),
        ]);
    }

    /**
     * Mark the action as failed.
     *
     * @param  Throwable|string|null  $exception
     * @return void
     */
    public function failedLog(Throwable|string|null $exception): void
    {
        $this->actionLog->failed($exception);
    }

    /**
     * Mark the action as finished if not failed.
     *
     * @return void
     */
    public function finishedLog(): void
    {
        if (!$this->isFailedLog()) {
            $this->actionLog->finished();
        }
    }

    /**
     * Mark batch action as finished where not failed.
     *
     * @return void
     */
    public function batchFinishedLog(): void
    {
        $this->actionLog->batchFinished();
    }

    /**
     * Check if the action is failed.
     *
     * @return bool
     */
    public function isFailedLog(): bool
    {
        return $this->actionLog->isFailed();
    }
}
