<?php

declare(strict_types=1);

namespace App\Enums\Models\Admin;

use App\Concerns\Enums\LocalizesName;
use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;

/**
 * Enum ActionLogStatus.
 */
enum ActionLogStatus: int implements HasColor
{
    use LocalizesName;

    case RUNNING = 0;
    case FAILED = 1;
    case FINISHED = 2;

    /**
     * Get the filament color for the enum.
     *
     * @return string|array
     */
    public function getColor(): string|array
    {
        return match ($this) {
            ActionLogStatus::RUNNING => Color::Amber,
            ActionLogStatus::FAILED => 'danger',
            ActionLogStatus::FINISHED => 'success',
        };
    }
}
