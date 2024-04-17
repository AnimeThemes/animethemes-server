<?php

declare(strict_types=1);

namespace App\Filament\HeaderActions\Models\Wiki\Artist;

use App\Actions\Models\Wiki\Artist\AttachArtistImageAction as AttachArtistImageActionAction;
use App\Filament\HeaderActions\Models\Wiki\AttachImageHeaderAction;
use Illuminate\Database\Eloquent\Model;

/**
 * Class AttachArtistImageHeaderAction.
 */
class AttachArtistImageHeaderAction extends AttachImageHeaderAction
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
