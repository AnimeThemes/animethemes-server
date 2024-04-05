<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Anime\RelationManagers;

use App\Enums\Models\Wiki\ResourceSite;
use App\Filament\Resources\BaseRelationManager;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use App\Pivots\Wiki\AnimeResource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Validation\Rules\Enum;

/**
 * Class ResourcesRelationManager.
 */
class ResourcesRelationManager extends BaseRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     *
     * @return string
     */
    protected static string $relationship = Anime::RELATION_RESOURCES;

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
        return $form
            ->schema([
                Select::make(ExternalResource::ATTRIBUTE_SITE)
                    ->label(__('filament.fields.external_resource.site.name'))
                    ->helperText(__('filament.fields.external_resource.site.help'))
                    ->options(ResourceSite::asSelectArray())
                    ->required()
                    ->rules(['required', new Enum(ResourceSite::class)]),
                    
                TextInput::make(ExternalResource::ATTRIBUTE_LINK)
                    ->label(__('filament.fields.external_resource.link.name'))
                    ->helperText(__('filament.fields.external_resource.link.help'))
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Set $set, ?string $state) {
                        if ($state !== null) {
                            $set(ExternalResource::ATTRIBUTE_SITE, ResourceSite::valueOf($state) ?? ResourceSite::OFFICIAL_SITE);
                            $set(ExternalResource::ATTRIBUTE_EXTERNAL_ID, ResourceSite::parseIdFromLink($state));
                        }
                    }),

                TextInput::make(ExternalResource::ATTRIBUTE_EXTERNAL_ID)
                    ->label(__('filament.fields.external_resource.external_id.name'))
                    ->helperText(__('filament.fields.external_resource.external_id.help'))
                    ->numeric()
                    ->rules(['nullable', 'integer']),

                TextInput::make(AnimeResource::ATTRIBUTE_AS)
                    ->label(__('filament.fields.anime.resources.as.name'))
                    ->helperText(__('filament.fields.anime.resources.as.help')),
            ]);
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
            ->recordTitleAttribute(ExternalResource::ATTRIBUTE_LINK)
            ->inverseRelationship(ExternalResource::RELATION_ANIME)
            ->columns([
                TextColumn::make(ExternalResource::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id'))
                    ->numeric(),

                SelectColumn::make(ExternalResource::ATTRIBUTE_SITE)
                    ->label(__('filament.fields.external_resource.site.name'))
                    ->options(ResourceSite::asSelectArray()),

                TextColumn::make(ExternalResource::ATTRIBUTE_LINK)
                    ->label(__('filament.fields.external_resource.link.name'))
                    ->searchable()
                    ->copyable(),

                TextColumn::make(AnimeResource::ATTRIBUTE_AS)
                    ->label(__('filament.fields.anime.resources.as.name')),

                TextColumn::make(ExternalResource::ATTRIBUTE_EXTERNAL_ID)
                    ->label(__('filament.fields.external_resource.external_id.name'))
                    ->numeric(),
            ])
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
