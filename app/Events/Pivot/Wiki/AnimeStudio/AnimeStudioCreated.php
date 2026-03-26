<?php

declare(strict_types=1);

namespace App\Events\Pivot\Wiki\AnimeStudio;

use App\Events\Base\Pivot\PivotCreatedEvent;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Studio;
use App\Pivots\Wiki\AnimeStudio;

/**
 * @extends PivotCreatedEvent<Studio, Anime>
 */
class AnimeStudioCreated extends PivotCreatedEvent
{
    public function __construct(AnimeStudio $animeStudio)
    {
        parent::__construct($animeStudio->studio, $animeStudio->anime);
    }
}
