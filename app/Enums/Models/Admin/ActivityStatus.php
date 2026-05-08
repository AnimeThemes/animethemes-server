<?php

declare(strict_types=1);

namespace App\Enums\Models\Admin;

use App\Concerns\Enums\LocalizesName;
use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ActivityStatus: int implements HasColor, HasLabel
{
    use LocalizesName;

    case RUNNING = 0;
    case FAILED = 1;
    case FINISHED = 2;

    /**
     * Get the filament color for the enum.
     *
     * @return string|array<int, string>
     */
    public function getColor(): string|array
    {
        return match ($this) {
            ActivityStatus::RUNNING => Color::Amber,
            ActivityStatus::FAILED => 'danger',
            ActivityStatus::FINISHED => 'success',
        };
    }
}
