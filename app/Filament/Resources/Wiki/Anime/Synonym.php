<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Anime;

use App\Filament\Resources\BaseRelationManager;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\Anime as AnimeResource;
use App\Filament\Resources\Wiki\Anime\Synonym\Pages\CreateSynonym;
use App\Filament\Resources\Wiki\Anime\Synonym\Pages\EditSynonym;
use App\Filament\Resources\Wiki\Anime\Synonym\Pages\ListSynonyms;
use App\Filament\Resources\Wiki\Anime\Synonym\Pages\ViewSynonym;
use App\Models\Wiki\Anime as AnimeModel;
use App\Models\Wiki\Anime\AnimeSynonym as SynonymModel;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Infolist;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * Class Synonym.
 */
class Synonym extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string|null
     */
    protected static ?string $model = SynonymModel::class;

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getLabel(): string
    {
        return __('filament.resources.singularLabel.anime_synonym');
    }

    /**
     * Get the displayable label of the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getPluralLabel(): string
    {
        return __('filament.resources.label.anime_synonyms');
    }

    /**
     * The logical group associated with the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getNavigationGroup(): string
    {
        return __('filament.resources.group.wiki');
    }

    /**
     * The icon displayed to the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getNavigationIcon(): string
    {
        return __('filament.resources.icon.anime_synonyms');
    }

    /**
     * Get the slug (URI key) for the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getSlug(): string
    {
        return 'anime-synonyms';
    }

    /**
     * Get the route key for the resource.
     *
     * @return string|null
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getRecordRouteKeyName(): ?string
    {
        return SynonymModel::ATTRIBUTE_ID;
    }

    /**
     * The form to the actions.
     *
     * @param  Form  $form
     * @return Form
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make(SynonymModel::ATTRIBUTE_ANIME)
                    ->label(__('filament.resources.singularLabel.anime'))
                    ->relationship(SynonymModel::RELATION_ANIME, AnimeModel::ATTRIBUTE_NAME)
                    ->searchable()
                    ->hiddenOn(BaseRelationManager::class),

                TextInput::make(SynonymModel::ATTRIBUTE_TEXT)
                    ->label(__('filament.fields.anime_synonym.text.name'))
                    ->helperText(__('filament.fields.anime_synonym.text.help'))
                    ->required()
                    ->maxLength(192)
                    ->rules(['required', 'max:192']),
            ])
            ->columns(1);
    }

    /**
     * The index page of the resource.
     *
     * @param  Table  $table
     * @return Table
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make(SynonymModel::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id'))
                    ->sortable(),

                TextColumn::make(SynonymModel::RELATION_ANIME.'.'.AnimeModel::ATTRIBUTE_NAME)
                    ->label(__('filament.resources.singularLabel.anime'))
                    ->toggleable()
                    ->urlToRelated(AnimeResource::class, SynonymModel::RELATION_ANIME),

                TextColumn::make(SynonymModel::ATTRIBUTE_TEXT)
                    ->label(__('filament.fields.anime_synonym.text.name'))
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort(SynonymModel::ATTRIBUTE_ID, 'desc')
            ->filters(static::getFilters())
            ->actions(static::getActions())
            ->bulkActions(static::getBulkActions());
    }

    /**
     * Get the infolist available for the resource.
     *
     * @param  Infolist  $infolist
     * @return Infolist
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make(__('filament.fields.base.timestamps'))
                    ->schema(parent::timestamps()),
            ]);
    }

    /**
     * Get the relationships available for the resource.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getRelations(): array
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getFilters(): array
    {
        return array_merge(
            parent::getFilters(),
            []
        );
    }

    /**
     * Get the actions available for the resource.
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
     * Get the bulk actions available for the resource.
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
     * Get the pages available for the resource.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getPages(): array
    {
        return [
            'index' => ListSynonyms::route('/'),
            'create' => CreateSynonym::route('/create'),
            'view' => ViewSynonym::route('/{record:synonym_id}'),
            'edit' => EditSynonym::route('/{record:synonym_id}/edit'),
        ];
    }
}
