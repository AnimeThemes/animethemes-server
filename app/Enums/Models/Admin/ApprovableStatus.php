<?php

declare(strict_types=1);

namespace App\Enums\Models\Admin;

use App\Concerns\Enums\LocalizesName;

/**
 * Enum ApprovableStatus.
 */
enum ApprovableStatus: int
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
    public function color(): string
    {
        return match ($this) {
            ApprovableStatus::PENDING => 'warning',
            ApprovableStatus::REJECTED => 'danger',
            ApprovableStatus::PARTIALLY_APPROVED => 'info',
            ApprovableStatus::APPROVED => 'success',
        };
    }
}
