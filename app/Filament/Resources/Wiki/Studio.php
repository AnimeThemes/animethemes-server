<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki;

use App\Filament\Actions\Models\Wiki\Studio\AttachStudioImageAction;
use App\Filament\Actions\Models\Wiki\Studio\AttachStudioResourceAction;
use App\Filament\Actions\Models\Wiki\Studio\BackfillStudioAction;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Fields\Slug;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\ExternalResource\RelationManagers\StudioResourceRelationManager;
use App\Filament\Resources\Wiki\Studio\Pages\CreateStudio;
use App\Filament\Resources\Wiki\Studio\Pages\EditStudio;
use App\Filament\Resources\Wiki\Studio\Pages\ListStudios;
use App\Filament\Resources\Wiki\Studio\Pages\ViewStudio;
use App\Filament\Resources\Wiki\Studio\RelationManagers\AnimeStudioRelationManager;
use App\Filament\Resources\Wiki\Studio\RelationManagers\ImageStudioRelationManager;
use App\Filament\Resources\Wiki\Studio\RelationManagers\ResourceStudioRelationManager;
use App\Models\Wiki\Studio as StudioModel;
use App\Pivots\Wiki\StudioResource;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Infolist;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;
use Illuminate\Support\Str;

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
     * The icon displayed to the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getNavigationIcon(): string
    {
        return __('filament.resources.icon.studios');
    }

    /**
     * Get the slug (URI key) for the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getRecordSlug(): string
    {
        return 'studios';
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

                Slug::make(StudioModel::ATTRIBUTE_SLUG)
                    ->label(__('filament.fields.studio.slug.name'))
                    ->helperText(__('filament.fields.studio.slug.help')),

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
                    ->sortable(),

                TextColumn::make(StudioModel::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.studio.name.name'))
                    ->sortable()
                    ->copyableWithMessage()
                    ->toggleable(),

                TextColumn::make(StudioModel::ATTRIBUTE_SLUG)
                    ->label(__('filament.fields.studio.slug.name'))
                    ->sortable()
                    ->toggleable(),

                TextColumn::make(StudioResource::ATTRIBUTE_AS)
                    ->label(__('filament.fields.studio.resources.as.name'))
                    ->visibleOn(StudioResourceRelationManager::class)
                    ->placeholder('-'),
            ])
            ->searchable();
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
                Section::make(static::getRecordTitle($infolist->getRecord()))
                    ->schema([
                        TextEntry::make(StudioModel::ATTRIBUTE_NAME)
                            ->label(__('filament.fields.studio.name.name'))
                            ->copyableWithMessage(),

                        TextEntry::make(StudioModel::ATTRIBUTE_SLUG)
                            ->label(__('filament.fields.studio.slug.name')),

                        TextEntry::make(StudioModel::ATTRIBUTE_ID)
                            ->label(__('filament.fields.base.id')),
                    ])
                    ->columns(3),

                Section::make(__('filament.fields.base.timestamps'))
                    ->schema(parent::timestamps())
                    ->columns(3),
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
        return [
            RelationGroup::make(static::getLabel(),
                array_merge(
                    [
                        AnimeStudioRelationManager::class,
                        ResourceStudioRelationManager::class,
                        ImageStudioRelationManager::class,
                    ],
                    parent::getBaseRelations(),
                )
            ),
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
            [],
            parent::getFilters(),
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
            [
                ActionGroup::make([
                    BackfillStudioAction::make('backfill-studio'),

                    AttachStudioImageAction::make('attach-studio-image'),

                    AttachStudioResourceAction::make('attach-studio-resource'),
                ])
            ],
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
     * Get the header actions available for the resource.
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
