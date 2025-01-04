<?php

declare(strict_types=1);

namespace App\Enums\Models\Admin;

use App\Concerns\Enums\LocalizesName;
use Filament\Support\Contracts\HasColor;

/**
 * Enum ApprovableStatus.
 */
enum ApprovableStatus: int implements HasColor
{
    use LocalizesName;

    case PENDING = 0;
    case REJECTED = 1;
    case PARTIALLY_APPROVED = 2;
    case APPROVED = 3;

    /**
     * Get the filament color for the enum.
     *
     * @return string|array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string}|null
     */
    public function getColor(): string|array|null
    {
        return match ($this) {
            ApprovableStatus::PENDING => 'warning',
            ApprovableStatus::REJECTED => 'danger',
            ApprovableStatus::PARTIALLY_APPROVED => 'info',
            ApprovableStatus::APPROVED => 'success',
        };
    }
}
