<?php

declare(strict_types=1);

namespace App\Filament\RelationManagers\Wiki;

use App\Filament\Actions\Models\Wiki\AttachImageAction;
use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\Wiki\Image as ImageResource;
use App\Models\Wiki\Image;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class ImageRelationManager.
 */
abstract class ImageRelationManager extends BaseRelationManager
{
    /**
     * The form to the actions.
     *
     * @param  Form  $form
     * @return Form
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function form(Form $form): Form
    {
        return ImageResource::form($form);
    }

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
                ->modifyQueryUsing(fn (Builder $query) => $query->with(ImageResource::getEloquentQuery()->getEagerLoads()))
                ->heading(ImageResource::getPluralLabel())
                ->modelLabel(ImageResource::getLabel())
                ->recordTitleAttribute(Image::ATTRIBUTE_PATH)
                ->columns(ImageResource::table($table)->getColumns())
                ->defaultSort(Image::TABLE . '.' . Image::ATTRIBUTE_ID, 'desc')
        );
    }

    /**
     * Get the filters available for the relation.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getFilters(): array
    {
        return array_merge(
            [],
            ImageResource::getFilters(),
        );
    }

    /**
     * Get the actions available for the relation.
     *
     * @return array
     */
    public static function getActions(): array
    {
        return array_merge(
            parent::getActions(),
            ImageResource::getActions(),
        );
    }

    /**
     * Get the bulk actions available for the relation.
     *
     * @param  array|null  $actionsIncludedInGroup
     * @return array
     */
    public static function getBulkActions(?array $actionsIncludedInGroup = []): array
    {
        return array_merge(
            parent::getBulkActions(),
            ImageResource::getBulkActions(),
        );
    }

    /**
     * Get the header actions available for the relation. These are merged with the table actions of the resources.
     *
     * @return array
     */
    public static function getHeaderActions(): array
    {
        return array_merge(
            parent::getHeaderActions(),
            ImageResource::getTableActions(),
            [AttachImageAction::make('attachimage')],
        );
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
