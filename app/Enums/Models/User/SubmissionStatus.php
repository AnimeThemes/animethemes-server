<?php

declare(strict_types=1);

namespace App\Enums\Models\User;

use App\Concerns\Enums\LocalizesName;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum SubmissionStatus: int implements HasColor, HasLabel
{
    use LocalizesName;

    case PENDING = 0;
    case CHANGES_REQUESTED = 1;
    case REJECTED = 2;
    case PARTIALLY_APPROVED = 3;
    case APPROVED = 4;

    /**
     * Get the filament color for the enum.
     */
    public function getColor(): string
    {
        return match ($this) {
            SubmissionStatus::PENDING,
            SubmissionStatus::CHANGES_REQUESTED => 'warning',
            SubmissionStatus::REJECTED => 'danger',
            SubmissionStatus::PARTIALLY_APPROVED => 'info',
            SubmissionStatus::APPROVED => 'success',
        };
    }
}
