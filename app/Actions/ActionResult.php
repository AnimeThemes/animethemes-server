<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\Actions\ActionStatus;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Class ActionResult.
 */
class ActionResult
{
    /**
     * Create a new action result instance.
     *
     * @param  ActionStatus  $status
     * @param  string|null  $message
     */
    public function __construct(protected readonly ActionStatus $status, protected readonly ?string $message = null)
    {
    }

    /**
     * Get the action result status.
     *
     * @return ActionStatus
     */
    public function getStatus(): ActionStatus
    {
        return $this->status;
    }

    /**
     * Get the action result message.
     *
     * @return string|null
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * Has the action failed?
     *
     * @return bool
     */
    public function hasFailed(): bool
    {
        return ActionStatus::FAILED()->is($this->status);
    }

    /**
     * Write results to log.
     *
     * @return void
     */
    public function toLog(): void
    {
        $this->hasFailed()
            ? Log::error($this->getMessage())
            : Log::info($this->getMessage());
    }

    /**
     * Write results to console output.
     *
     * @param  Command  $command
     * @return void
     */
    public function toConsole(Command $command): void
    {
        $this->hasFailed()
            ? $command->error($this->getMessage())
            : $command->info($this->getMessage());
    }
}
