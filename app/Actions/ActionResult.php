<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\Actions\ActionStatus;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ActionResult
{
    public function __construct(protected readonly ActionStatus $status, protected readonly ?string $message = null) {}

    /**
     * Get the action result status.
     */
    public function getStatus(): ActionStatus
    {
        return $this->status;
    }

    /**
     * Get the action result message.
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * Has the action failed?
     */
    public function hasFailed(): bool
    {
        return $this->status === ActionStatus::FAILED;
    }

    /**
     * Write results to log.
     */
    public function toLog(): void
    {
        $this->hasFailed()
            ? Log::error($this->getMessage())
            : Log::info($this->getMessage());
    }

    /**
     * Write results to console output.
     */
    public function toConsole(Command $command): void
    {
        $this->hasFailed()
            ? $command->error($this->getMessage())
            : $command->info($this->getMessage());
    }
}
