<?php

declare(strict_types=1);

namespace App\Enums\Models\Admin;

use App\Concerns\Enums\LocalizesName;
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
     * @return string|array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string}|null
     */
    public function getColor(): string|array|null
    {
        return match ($this) {
            ActionLogStatus::RUNNING => 'primary',
            ActionLogStatus::FAILED => 'danger',
            ActionLogStatus::FINISHED => 'success',
        };
    }
}
