<?php

declare(strict_types=1);

namespace App\Filament\HeaderActions\Models\Wiki\Anime;

use App\Actions\Models\Wiki\Anime\AttachAnimeImageAction as AttachAnimeImageActionAction;
use App\Filament\HeaderActions\Models\Wiki\AttachImageHeaderAction;
use App\Models\Wiki\Anime;

/**
 * Class AttachAnimeImageHeaderAction.
 */
class AttachAnimeImageHeaderAction extends AttachImageHeaderAction
{
    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->action(fn (Anime $record, array $data) => (new AttachAnimeImageActionAction($this->facets))->handle($record, $data));
    }
}
