<?php

declare(strict_types=1);

namespace App\Filament\Actions\Models\Wiki\Artist;

use App\Actions\Models\Wiki\Artist\AttachArtistImageAction as AttachArtistImageActionAction;
use App\Filament\Actions\Models\Wiki\AttachImageAction;
use Illuminate\Database\Eloquent\Model;

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

        $this->action(fn (Model $record, array $data) => (new AttachArtistImageActionAction($this->facets))->handle($record, $data));
    }
}
