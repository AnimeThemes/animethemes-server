<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki;

use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\Artist\Pages\CreateArtist;
use App\Filament\Resources\Wiki\Artist\Pages\EditArtist;
use App\Filament\Resources\Wiki\Artist\Pages\ListArtists;
use App\Filament\Resources\Wiki\Artist\Pages\ViewArtist;
use App\Filament\Resources\Wiki\Artist\RelationManagers\ResourceArtistRelationManager;
use App\Filament\Resources\Wiki\ExternalResource\RelationManagers\ArtistResourceRelationManager;
use App\Models\Wiki\Artist as ArtistModel;
use App\Pivots\Wiki\ArtistResource;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Class Artist.
 */
class Artist extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string|null
     */
    protected static ?string $model = ArtistModel::class;

    /**
     * The icon displayed to the resource.
     *
     * @var string|null
     */
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getLabel(): string
    {
        return __('filament.resources.singularLabel.artist');
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
        return __('filament.resources.label.artists');
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
                TextInput::make(ArtistModel::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.artist.name.name'))
                    ->helperText(__('filament.fields.artist.name.help'))
                    ->required()
                    ->rules(['required', 'max:192'])
                    ->maxLength(192)
                    ->live()
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set(ArtistModel::ATTRIBUTE_SLUG, Str::slug($state, '_'))),

                TextInput::make(ArtistModel::ATTRIBUTE_SLUG)
                    ->label(__('filament.fields.artist.slug.name'))
                    ->helperText(__('filament.fields.artist.slug.help'))
                    ->required()
                    ->maxLength(192)
                    ->rules(['required', 'max:192', 'alpha_dash', Rule::unique(ArtistModel::class)]),

                TextInput::make(ArtistResource::ATTRIBUTE_AS)
                    ->label(__('filament.fields.artist.resources.as.name'))
                    ->helperText(__('filament.fields.artist.resources.as.help'))
                    ->visibleOn(ArtistResourceRelationManager::class),
            ])
            ->columns(2);
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
                TextColumn::make(ArtistModel::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id'))
                    ->numeric()
                    ->sortable(),

                TextColumn::make(ArtistModel::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.artist.name.name'))
                    ->sortable()
                    ->searchable()
                    ->copyable(),

                TextColumn::make(ArtistModel::ATTRIBUTE_SLUG)
                    ->label(__('filament.fields.artist.slug.name'))
                    ->sortable()
                    ->copyable(),

                TextColumn::make(ArtistResource::ATTRIBUTE_AS)
                    ->label(__('filament.fields.artist.resources.as.name'))
                    ->visibleOn(ArtistResourceRelationManager::class),
            ])
            ->filters(static::getFilters())
            ->actions(static::getActions())
            ->bulkActions(static::getBulkActions());
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
        return [
            ResourceArtistRelationManager::class,
        ];
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
            'index' => ListArtists::route('/'),
            'create' => CreateArtist::route('/create'),
            'view' => ViewArtist::route('/{record}'),
            'edit' => EditArtist::route('/{record}/edit'),
        ];
    }
}
