<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Video\Script\Pages;

use App\Filament\HeaderActions\Storage\Wiki\Video\Script\DeleteScriptHeaderAction;
use App\Filament\HeaderActions\Storage\Wiki\Video\Script\MoveScriptHeaderAction;
use App\Filament\Resources\Wiki\Video\Script;
use App\Filament\Resources\Base\BaseEditResource;
use App\Models\Wiki\Video\VideoScript as ScriptModel;
use Filament\Actions\ActionGroup;
use Filament\Support\Enums\MaxWidth;

/**
 * Class EditScript.
 */
class EditScript extends BaseEditResource
{
    protected static string $resource = Script::class;

    /**
     * Get the header actions available.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    protected function getHeaderActions(): array
    {
        return array_merge(
            parent::getHeaderActions(),
            [
                ActionGroup::make([
                    MoveScriptHeaderAction::make('move-script'),
                    
                    DeleteScriptHeaderAction::make('delete-script'),
                ]),
            ],
        );
    }
}
