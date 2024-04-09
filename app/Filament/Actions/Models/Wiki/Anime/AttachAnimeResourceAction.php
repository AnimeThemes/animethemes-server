<?php

declare(strict_types=1);

namespace App\Filament\Actions\Models\Wiki\Anime;

use App\Actions\Models\Wiki\Anime\AttachAnimeResourceAction as AttachAnimeResourceActionAction;
use App\Filament\Actions\Models\Wiki\AttachResourceAction;
use Illuminate\Database\Eloquent\Model;

/**
 * Class AttachAnimeResourceAction.
 */
class AttachAnimeResourceAction extends AttachResourceAction
{
    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->action(fn (Model $record, array $data) => (new AttachAnimeResourceActionAction($this->sites))->handle($record, $data));
    }
}
