<?php

declare(strict_types=1);

namespace App\Filament\Actions\Models\Wiki\Image;

use App\Actions\Models\Wiki\Image\OptimizeImageAction as OptimizeImage;
use App\Filament\Actions\BaseAction;
use App\Filament\Components\Fields\Select;
use App\Filament\Components\Fields\TextInput;
use App\Models\Wiki\Image;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Arr;
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

        $this->action(fn (Image $record, array $data) => $this->handle($record, $data));
    }

    public function handle(Image $image, array $fields): void
    {
        $extension = Arr::get($fields, 'extension');
        $width = ($value = Arr::get($fields, 'width')) !== null ? (int) $value : null;
        $height = ($value = Arr::get($fields, 'height')) !== null ? (int) $value : null;

        $action = new OptimizeImage($image, $extension, $width, $height);

        $actionResult = $action->handle();

        if ($actionResult->hasFailed()) {
            $this->failedLog($actionResult->getMessage());
        }
    }

    public function getSchema(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('extension')
                    ->label(__('filament.actions.models.wiki.optimize_image.extension.name'))
                    ->helperText(__('filament.actions.models.wiki.optimize_image.extension.help'))
                    ->options([
                        'avif' => 'avif',
                    ])
                    ->default('avif'),

                TextInput::make('width')
                    ->label(__('filament.actions.models.wiki.optimize_image.width.name'))
                    ->helperText(__('filament.actions.models.wiki.optimize_image.width.help'))
                    ->integer(),

                TextInput::make('height')
                    ->label(__('filament.actions.models.wiki.optimize_image.height.name'))
                    ->helperText(__('filament.actions.models.wiki.optimize_image.height.help'))
                    ->integer(),
            ])
            ->columns(1);
    }
}
