<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\Wiki\Anime\Theme\Entry;

use App\GraphQL\Schema\Fields\Base\Aggregate\CountField;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;

class AnimeThemeEntryTracksCountField extends CountField
{
    public function __construct()
    {
        parent::__construct(AnimeThemeEntry::RELATION_TRACKS, 'tracksCount');
    }

    public function description(): string
    {
        return 'The number of tracks belonging to the resource';
    }
}
