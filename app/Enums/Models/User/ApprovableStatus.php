<?php

declare(strict_types=1);

namespace App\Enums\Models\User;

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
     * @return string
     */
    public function getColor(): string
    {
        return match ($this) {
            ApprovableStatus::PENDING => 'warning',
            ApprovableStatus::REJECTED => 'danger',
            ApprovableStatus::PARTIALLY_APPROVED => 'info',
            ApprovableStatus::APPROVED => 'success',
        };
    }
}
