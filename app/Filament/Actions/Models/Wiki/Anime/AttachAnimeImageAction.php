<?php

declare(strict_types=1);

namespace App\Filament\Actions\Models\Wiki\Anime;

use App\Actions\Models\Wiki\Anime\AttachAnimeImageAction as AttachAnimeImageActionAction;
use App\Filament\Actions\Models\Wiki\AttachImageAction;
use Illuminate\Database\Eloquent\Model;

/**
 * Class AttachAnimeImageAction.
 */
class AttachAnimeImageAction extends AttachImageAction
{
    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->action(fn (Model $record, array $data) => (new AttachAnimeImageActionAction($this->facets))->handle($record, $data));
    }
}