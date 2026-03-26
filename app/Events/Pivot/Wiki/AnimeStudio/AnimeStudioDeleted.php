<?php

declare(strict_types=1);

namespace App\Events\Pivot\Wiki\AnimeStudio;

use App\Events\Base\Pivot\PivotDeletedEvent;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Studio;
use App\Pivots\Wiki\AnimeStudio;

/**
 * @extends PivotDeletedEvent<Studio, Anime>
 */
class AnimeStudioDeleted extends PivotDeletedEvent
{
    public function __construct(AnimeStudio $animeStudio)
    {
        parent::__construct($animeStudio->studio, $animeStudio->anime);
    }
}
