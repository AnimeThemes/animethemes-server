<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Anime\Theme\Entry\RelationManagers;

use App\Filament\Resources\BaseRelationManager;
use App\Filament\Resources\Wiki\Video as VideoResource;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use Filament\Forms\Form;
use Filament\Tables\Table;

/**
 * Class VideoEntryRelationManager.
 */
class VideoEntryRelationManager extends BaseRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     *
     * @return string
     */
    protected static string $relationship = AnimeThemeEntry::RELATION_VIDEOS;

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
        return VideoResource::form($form);
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
            ->heading(VideoResource::getPluralLabel())
            ->modelLabel(VideoResource::getLabel())
            ->recordTitleAttribute(Video::ATTRIBUTE_FILENAME)
            ->inverseRelationship(Video::RELATION_ANIMETHEMEENTRIES)
            ->columns(VideoResource::table($table)->getColumns())
            ->defaultSort(Video::TABLE.'.'.Video::ATTRIBUTE_ID, 'desc')
            ->filters(static::getFilters())
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
