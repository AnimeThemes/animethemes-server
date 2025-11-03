<?php

declare(strict_types=1);

namespace App\Filament\Actions\Models\Wiki\Image;

use App\Actions\Models\Wiki\Image\OptimizeImageAction as OptimizeImage;
use App\Filament\Actions\BaseAction;
use App\Models\Wiki\Image;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;

class OptimizeImageAction extends BaseAction
{
    public static function getDefaultName(): ?string
    {
        return 'optimize-image';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.models.wiki.optimize_image.name'));

        $this->icon(Heroicon::ArrowPathRoundedSquare);

        $this->visible(fn (Image $record) => Auth::user()->can('update', $record));

        $this->action(fn (Image $record) => $this->handle($record));
    }

    public function handle(Image $image): void
    {
        $action = new OptimizeImage($image);

        $actionResult = $action->handle();

        if ($actionResult->hasFailed()) {
            $this->failedLog($actionResult->getMessage());
        }
    }
}
