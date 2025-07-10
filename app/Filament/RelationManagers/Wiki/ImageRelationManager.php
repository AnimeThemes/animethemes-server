<?php

declare(strict_types=1);

namespace App\Filament\RelationManagers\Wiki;

use App\Filament\Actions\Models\Wiki\Image\AttachImageAction;
use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\Image as ImageResource;
use App\Models\Wiki\Image;
use Filament\Tables\Table;

/**
 * Class ImageRelationManager.
 */
abstract class ImageRelationManager extends BaseRelationManager
{
    /**
     * The resource of the relation manager.
     *
     * @var class-string<BaseResource>|null
     */
    protected static ?string $relatedResource = ImageResource::class;

    /**
     * The index page of the resource.
     *
     * @param  Table  $table
     * @return Table
     */
    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->recordTitleAttribute(Image::ATTRIBUTE_PATH)
                ->columns(ImageResource::table($table)->getColumns())
                ->defaultSort(Image::TABLE.'.'.Image::ATTRIBUTE_ID, 'desc')
        );
    }

    /**
     * Get the actions available for the relation.
     *
     * @return array
     */
    public static function getRecordActions(): array
    {
        return [
            ...parent::getRecordActions(),
            ...ImageResource::getActions(),
        ];
    }

    /**
     * Get the bulk actions available for the relation.
     *
     * @param  array|null  $actionsIncludedInGroup
     * @return array
     */
    public static function getBulkActions(?array $actionsIncludedInGroup = []): array
    {
        return [
            ...parent::getBulkActions(),
            ...ImageResource::getBulkActions(),
        ];
    }

    /**
     * Get the header actions available for the relation.
     * These are merged with the table actions of the resources.
     *
     * @return array
     */
    public static function getHeaderActions(): array
    {
        return [
            ...parent::getHeaderActions(),
            ...ImageResource::getTableActions(),

            AttachImageAction::make(),
        ];
    }

    /**
     * Determine whether the related model can be created.
     *
     * @return bool
     */
    protected function canCreate(): bool
    {
        return false;
    }
}
