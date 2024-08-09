<?php

declare(strict_types=1);

namespace App\Concerns\Models;

use App\Enums\Models\Wiki\AnimeSynonymType;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeSynonym;
use Illuminate\Support\Facades\Log;

/**
 * Trait CanCreateAnimeSynonym.
 */
trait CanCreateAnimeSynonym
{
    /**
     * Create Anime Synonym for the Anime.
     *
     * @param  string|null  $text
     * @param  int  $type
     * @param  Anime  $anime
     * @return void
     */
    public function createAnimeSynonym(?string $text, int $type, Anime $anime): void
    {
        if (
            $text === null
            || empty($text)
            || ($type === AnimeSynonymType::OTHER->value && $text === $anime->getName())
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
