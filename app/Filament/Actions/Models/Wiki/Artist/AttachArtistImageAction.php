<?php

declare(strict_types=1);

namespace App\Filament\Actions\Models\Wiki\Artist;

use App\Actions\Models\Wiki\Artist\AttachArtistImageAction as AttachArtistImageActionAction;
use App\Enums\Models\Wiki\ImageFacet;
use App\Filament\Actions\Models\Wiki\AttachImageAction;
use App\Models\Wiki\Artist;

/**
 * Class AttachArtistImageAction.
 */
class AttachArtistImageAction extends AttachImageAction
{
    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->facets([
            ImageFacet::COVER_SMALL,
            ImageFacet::COVER_LARGE,
        ]);

        $this->action(fn (Artist $record, array $data) => (new AttachArtistImageActionAction($this->facets))->handle($record, $data));
    }
}
