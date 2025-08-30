<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\Actions\ActionStatus;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ActionResult
{
    public function __construct(protected readonly ActionStatus $status, protected readonly ?string $message = null) {}

    public function getStatus(): ActionStatus
    {
        return $this->status;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function hasFailed(): bool
    {
        return $this->status === ActionStatus::FAILED;
    }

    public function toLog(): void
    {
        $this->hasFailed()
            ? Log::error($this->getMessage())
            : Log::info($this->getMessage());
    }

    public function toConsole(Command $command): void
    {
        $this->hasFailed()
            ? $command->error($this->getMessage())
            : $command->info($this->getMessage());
    }
}
