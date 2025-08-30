<?php

declare(strict_types=1);

namespace App\Concerns\Models;

use App\Enums\Models\Wiki\AnimeSynonymType;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeSynonym;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

trait CanCreateAnimeSynonym
{
    public function createAnimeSynonym(?string $text, int $type, Anime $anime): void
    {
        if (
            blank($text)
            || ($type === AnimeSynonymType::OTHER->value && Str::is($text, $anime->getName(), true))
        ) {
            return;
        }

        Log::info("Creating {$text} for Anime {$anime->getName()}");

        AnimeSynonym::query()->create([
            AnimeSynonym::ATTRIBUTE_TEXT => $text,
            AnimeSynonym::ATTRIBUTE_TYPE => $type,
            AnimeSynonym::ATTRIBUTE_ANIME => $anime->getKey(),
        ]);
    }
}
