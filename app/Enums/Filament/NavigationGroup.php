<?php

declare(strict_types=1);

namespace App\Enums\Filament;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum NavigationGroup implements HasLabel
{
    case CONTENT;
    case ADMIN;
    case AUTH;
    case DOCUMENT;
    case DISCORD;
    case LIST;
    case USER;

    public function getLabel(): string
    {
        return Str::of($this->name)
            ->lower()
            ->pascal()
            ->__toString();
    }
}
