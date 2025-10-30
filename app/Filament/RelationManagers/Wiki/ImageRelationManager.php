<?php

declare(strict_types=1);

namespace App\Filament\RelationManagers\Wiki;

use App\Filament\Actions\Models\Wiki\Image\AttachImageAction;
use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\Image as ImageResource;
use App\Models\Wiki\Image;
use App\Pivots\Morph\Imageable;
use Filament\Tables\Table;

class ImageRelationManager extends BaseRelationManager
{
    /**
     * The resource of the relation manager.
     *
     * @var class-string<BaseResource>|null
     */
    protected static ?string $relatedResource = ImageResource::class;

    /**
     * The relationship the relation manager corresponds to.
     */
    protected static string $relationship = 'images';

    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->recordTitleAttribute(Image::ATTRIBUTE_PATH)
                ->reorderable(Imageable::ATTRIBUTE_DEPTH)
                ->defaultSort(Imageable::ATTRIBUTE_DEPTH)
        );
    }

    /**
     * Get the header actions available for the relation.
     *
     * @return \Filament\Actions\Action[]
     */
    public static function getHeaderActions(): array
    {
        return [
            ...parent::getHeaderActions(),

            AttachImageAction::make(),
        ];
    }

    public function canCreate(): bool
    {
        return false;
    }
}
