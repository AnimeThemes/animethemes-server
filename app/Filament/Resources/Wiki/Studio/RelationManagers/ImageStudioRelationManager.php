<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Studio\RelationManagers;

use App\Filament\Resources\BaseRelationManager;
use App\Filament\Resources\Wiki\Image as ImageResource;
use App\Models\Wiki\Studio;
use App\Models\Wiki\Image;
use Filament\Forms\Form;
use Filament\Tables\Table;

/**
 * Class ImageStudioRelationManager.
 */
class ImageStudioRelationManager extends BaseRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     *
     * @return string
     */
    protected static string $relationship = Studio::RELATION_IMAGES;

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
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function table(Table $table): Table
    {
        return $table
            ->heading(ImageResource::getPluralLabel())
            ->modelLabel(ImageResource::getLabel())
            ->recordTitleAttribute(Image::ATTRIBUTE_PATH)
            ->inverseRelationship(Image::RELATION_STUDIOS)
            ->columns(ImageResource::table($table)->getColumns())
            ->defaultSort(Image::TABLE.'.'.Image::ATTRIBUTE_ID, 'desc')
            ->filters(static::getFilters())
            ->filtersFormMaxHeight('400px')
            ->headerActions(static::getHeaderActions())
            ->actions(static::getActions())
            ->bulkActions(static::getBulkActions());
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
            parent::getFilters(),
            [],
        );
    }

    /**
     * Get the actions available for the relation.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getActions(): array
    {
        return array_merge(
            parent::getActions(),
            [],
        );
    }

    /**
     * Get the bulk actions available for the relation.
     * 
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getBulkActions(): array
    {
        return array_merge(
            parent::getBulkActions(),
            [],
        );
    }

    /**
     * Get the header actions available for the relation.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getHeaderActions(): array
    {
        return array_merge(
            parent::getHeaderActions(),
            [],
        );
    }
}
