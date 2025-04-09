<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Video\Script\Pages;

use App\Filament\HeaderActions\Storage\Wiki\Video\Script\DeleteScriptHeaderAction;
use App\Filament\HeaderActions\Storage\Wiki\Video\Script\MoveScriptHeaderAction;
use App\Filament\Resources\Base\BaseViewResource;
use App\Filament\Resources\Wiki\Video\Script;
use Filament\Actions\ActionGroup;

/**
 * Class ViewScript.
 */
class ViewScript extends BaseViewResource
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
        return [
            ...parent::getHeaderActions(),

            ActionGroup::make([
                MoveScriptHeaderAction::make('move-script'),

                DeleteScriptHeaderAction::make('delete-script'),
            ]),
        ];
    }
}
