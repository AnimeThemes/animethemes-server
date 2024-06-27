<?php

declare(strict_types=1);

namespace App\Enums\Actions;

use App\Concerns\Enums\LocalizesName;

/**
 * Enum ActionLogStatus.
 */
enum ActionLogStatus: int
{
    use LocalizesName;

    case RUNNING = 0;
    case FAILED = 1;
    case FINISHED = 2;

    /**
     * Get the filament color for the enum.
     *
     * @return string
     */
    public function color(): string
    {
        return match ($this) {
            ActionLogStatus::RUNNING => 'primary',
            ActionLogStatus::FAILED => 'danger',
            ActionLogStatus::FINISHED => 'success',
        };
    }
}
