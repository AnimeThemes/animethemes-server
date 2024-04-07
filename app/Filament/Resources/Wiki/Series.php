<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki;

use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\Series\Pages\CreateSeries;
use App\Filament\Resources\Wiki\Series\Pages\EditSeries;
use App\Filament\Resources\Wiki\Series\Pages\ListSeries;
use App\Filament\Resources\Wiki\Series\Pages\ViewSeries;
use App\Filament\Resources\Wiki\Series\RelationManagers\AnimeSeriesRelationManager;
use App\Models\Wiki\Series as SeriesModel;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Class Series.
 */
class Series extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string|null
     */
    protected static ?string $model = SeriesModel::class;

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
        return __('filament.resources.singularLabel.series');
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
        return __('filament.resources.label.series');
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
     * Get the slug (URI key) for the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getSlug(): string
    {
        return 'series';
    }

    /**
     * Get the title attribute for the resource.
     *
     * @return string|null
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getRecordTitleAttribute(): ?string
    {
        return SeriesModel::ATTRIBUTE_NAME;
    }

    /**
     * Get the attributes available for the global search.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getGloballySearchableAttributes(): array
    {
        return [SeriesModel::ATTRIBUTE_NAME];
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
        return SeriesModel::ATTRIBUTE_ID;
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
                TextInput::make(SeriesModel::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.series.name.name'))
                    ->helperText(__('filament.fields.series.name.help'))
                    ->required()
                    ->maxLength(192)
                    ->rules(['required', 'max:192'])
                    ->live()
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set(SeriesModel::ATTRIBUTE_SLUG, Str::slug($state, '_'))),

                TextInput::make(SeriesModel::ATTRIBUTE_SLUG)
                    ->label(__('filament.fields.series.slug.name'))
                    ->helperText(__('filament.fields.series.slug.help'))
                    ->required()
                    ->maxLength(192)
                    ->rules(['required', 'max:192', 'alpha_dash', Rule::unique(SeriesModel::class)]),
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
                TextColumn::make(SeriesModel::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id'))
                    ->numeric()
                    ->sortable(),

                TextColumn::make(SeriesModel::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.series.name.name'))
                    ->sortable()
                    ->searchable()
                    ->copyable(),

                TextColumn::make(SeriesModel::ATTRIBUTE_SLUG)
                    ->label(__('filament.fields.series.slug.name'))
                    ->sortable()
                    ->copyable(),
            ])
            ->defaultSort(SeriesModel::ATTRIBUTE_ID, 'desc')
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
            AnimeSeriesRelationManager::class,
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
            'index' => ListSeries::route('/'),
            'create' => CreateSeries::route('/create'),
            'view' => ViewSeries::route('/{record:series_id}'),
            'edit' => EditSeries::route('/{record:series_id}/edit'),
        ];
    }
}
