<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki;

use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\ExternalResource\RelationManagers\StudioResourceRelationManager;
use App\Filament\Resources\Wiki\Studio\Pages\CreateStudio;
use App\Filament\Resources\Wiki\Studio\Pages\EditStudio;
use App\Filament\Resources\Wiki\Studio\Pages\ListStudios;
use App\Filament\Resources\Wiki\Studio\Pages\ViewStudio;
use App\Filament\Resources\Wiki\Studio\RelationManagers\AnimeStudioRelationManager;
use App\Filament\Resources\Wiki\Studio\RelationManagers\ImageStudioRelationManager;
use App\Filament\Resources\Wiki\Studio\RelationManagers\ResourcesRelationManager;
use App\Filament\Resources\Wiki\Studio\RelationManagers\ResourceStudioRelationManager;
use App\Models\Wiki\Studio as StudioModel;
use App\Pivots\Wiki\StudioResource;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Class Studio.
 */
class Studio extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string|null
     */
    protected static ?string $model = StudioModel::class;

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
        return __('filament.resources.singularLabel.studio');
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
        return __('filament.resources.label.studios');
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
        return 'studios';
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
        return StudioModel::ATTRIBUTE_ID;
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
                TextInput::make(StudioModel::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.studio.name.name'))
                    ->helperText(__('filament.fields.studio.name.help'))
                    ->required()
                    ->maxLength(192)
                    ->rules(['required', 'max:192'])
                    ->live(true)
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set(StudioModel::ATTRIBUTE_SLUG, Str::slug($state, '_'))),

                TextInput::make(StudioModel::ATTRIBUTE_SLUG)
                    ->label(__('filament.fields.studio.slug.name'))
                    ->helperText(__('filament.fields.studio.slug.help'))
                    ->required()
                    ->maxLength(192)
                    ->rules(['required', 'max:192', 'alpha_dash', Rule::unique(StudioModel::class)]),

                TextInput::make(StudioResource::ATTRIBUTE_AS)
                    ->label(__('filament.fields.studio.resources.as.name'))
                    ->helperText(__('filament.fields.studio.resources.as.help'))
                    ->visibleOn(StudioResourceRelationManager::class),
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
    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make(StudioModel::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id'))
                    ->numeric()
                    ->sortable(),

                TextColumn::make(StudioModel::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.studio.name.name'))
                    ->sortable()
                    ->searchable()
                    ->copyable(),

                TextColumn::make(StudioModel::ATTRIBUTE_SLUG)
                    ->label(__('filament.fields.studio.slug.name'))
                    ->sortable()
                    ->copyable(),

                TextColumn::make(StudioResource::ATTRIBUTE_AS)
                    ->label(__('filament.fields.studio.resources.as.name'))
                    ->visibleOn(StudioResourceRelationManager::class),
            ])
            ->defaultSort(StudioModel::ATTRIBUTE_ID, 'desc')
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
            RelationGroup::make(static::getLabel(), [
                AnimeStudioRelationManager::class,
                ResourceStudioRelationManager::class,
                ImageStudioRelationManager::class,
            ]),
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
            'index' => ListStudios::route('/'),
            'create' => CreateStudio::route('/create'),
            'view' => ViewStudio::route('/{record:studio_id}'),
            'edit' => EditStudio::route('/{record:studio_id}/edit'),
        ];
    }
}
