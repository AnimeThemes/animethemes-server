<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Studio\RelationManagers;

use App\Filament\Resources\BaseRelationManager;
use App\Filament\Resources\Wiki\Anime as AnimeResource;
use App\Models\Wiki\Studio;
use App\Models\Wiki\Anime;
use Filament\Forms\Form;
use Filament\Tables\Table;

/**
 * Class AnimeStudioRelationManager.
 */
class AnimeStudioRelationManager extends BaseRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     *
     * @return string
     */
    protected static string $relationship = Studio::RELATION_ANIME;

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
        return AnimeResource::form($form);
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
            ->heading(AnimeResource::getPluralLabel())
            ->modelLabel(AnimeResource::getLabel())
            ->recordTitleAttribute(Anime::ATTRIBUTE_NAME)
            ->inverseRelationship(Anime::RELATION_STUDIOS)
            ->columns(AnimeResource::table($table)->getColumns())
            ->defaultSort(Anime::TABLE.'.'.Anime::ATTRIBUTE_ID, 'desc');
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
