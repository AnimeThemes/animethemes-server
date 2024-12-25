<?php

declare(strict_types=1);

namespace App\Filament\Resources\List\External;

use App\Enums\Models\List\ExternalEntryWatchStatus;
use App\Filament\Components\Columns\BelongsToColumn;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Fields\BelongsTo;
use App\Filament\Components\Fields\Select;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\List\External\ExternalEntry\Pages\EditExternalEntry;
use App\Filament\Resources\List\External\ExternalEntry\Pages\ListExternalEntries;
use App\Filament\Resources\List\External\ExternalEntry\Pages\ViewExternalEntry;
use App\Filament\Resources\List\External\RelationManagers\ExternalEntryExternalProfileRelationManager;
use App\Filament\Resources\List\ExternalProfile as ExternalProfileResource;
use App\Filament\Resources\Wiki\Anime;
use App\Models\List\External\ExternalEntry as ExternalEntryModel;
use App\Models\List\ExternalProfile;
use App\Models\Wiki\Anime as AnimeModel;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Infolist;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use Illuminate\Validation\Rules\Enum;

/**
 * Class ExternalEntry.
 */
class ExternalEntry extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string|null
     */
    protected static ?string $model = ExternalEntryModel::class;

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getLabel(): string
    {
        return __('filament.resources.singularLabel.external_entry');
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
        return __('filament.resources.label.external_entries');
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
        return __('filament.resources.group.list');
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
        return __('filament.resources.icon.external_entries');
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
        return 'external-entries';
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
                BelongsTo::make(ExternalEntryModel::ATTRIBUTE_PROFILE)
                    ->resource(ExternalProfileResource::class)
                    ->required()
                    ->rules(['required'])
                    ->hiddenOn([ExternalEntryExternalProfileRelationManager::class]),

                BelongsTo::make(ExternalEntryModel::ATTRIBUTE_ANIME)
                    ->resource(Anime::class)
                    ->required()
                    ->rules(['required']),

                TextInput::make(ExternalEntryModel::ATTRIBUTE_SCORE)
                    ->label(__('filament.fields.external_entry.score.name'))
                    ->helperText(__('filament.fields.external_entry.score.help'))
                    ->numeric(),

                Select::make(ExternalEntryModel::ATTRIBUTE_WATCH_STATUS)
                    ->label(__('filament.fields.external_entry.watch_status.name'))
                    ->helperText(__('filament.fields.external_entry.watch_status.help'))
                    ->options(ExternalEntryWatchStatus::asSelectArray())
                    ->rules([new Enum(ExternalEntryWatchStatus::class)]),

                Checkbox::make(ExternalEntryModel::ATTRIBUTE_IS_FAVORITE)
                    ->label(__('filament.fields.external_entry.is_favorite.name'))
                    ->helperText(__('filament.fields.external_entry.is_favorite.help'))
                    ->rules(['boolean']),
            ])
            ->columns(1);
    }

    /**
     * The index page of the resource.
     *
     * @param  Table  $table
     * @return Table
     */
    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                BelongsToColumn::make(ExternalEntryModel::RELATION_PROFILE.'.'.ExternalProfile::ATTRIBUTE_NAME)
                    ->resource(ExternalProfileResource::class),

                BelongsToColumn::make(ExternalEntryModel::RELATION_ANIME.'.'.AnimeModel::ATTRIBUTE_NAME)
                    ->resource(Anime::class),

                IconColumn::make(ExternalEntryModel::ATTRIBUTE_IS_FAVORITE)
                    ->label(__('filament.fields.external_entry.is_favorite.name'))
                    ->boolean(),

                TextColumn::make(ExternalEntryModel::ATTRIBUTE_SCORE)
                    ->label(__('filament.fields.external_entry.score.name')),

                TextColumn::make(ExternalEntryModel::ATTRIBUTE_WATCH_STATUS)
                    ->label(__('filament.fields.external_entry.watch_status.name'))
                    ->formatStateUsing(fn (ExternalEntryWatchStatus $state) => $state->localize()),

                TextColumn::make(ExternalEntryModel::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id')),
            ]);
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
                        TextEntry::make(ExternalEntryModel::ATTRIBUTE_PROFILE)
                            ->label(__('filament.resources.singularLabel.external_profile'))
                            ->urlToRelated(ExternalProfileResource::class, ExternalEntryModel::RELATION_PROFILE, true),

                        TextEntry::make(ExternalEntryModel::ATTRIBUTE_ANIME)
                            ->label(__('filament.resources.singularLabel.anime'))
                            ->urlToRelated(Anime::class, ExternalEntryModel::RELATION_ANIME, true),

                        IconEntry::make(ExternalEntryModel::ATTRIBUTE_IS_FAVORITE)
                            ->label(__('filament.fields.external_entry.is_favorite.name')),

                        TextEntry::make(ExternalEntryModel::ATTRIBUTE_SCORE)
                            ->label(__('filament.fields.external_entry.score.name')),

                        TextEntry::make(ExternalEntryModel::ATTRIBUTE_WATCH_STATUS)
                            ->label(__('filament.fields.external_entry.watch_status.name'))
                            ->formatStateUsing(fn (ExternalEntryWatchStatus $state) => $state->localize()),

                        TextEntry::make(ExternalEntryModel::ATTRIBUTE_ID)
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
            RelationGroup::make(
                static::getLabel(),
                array_merge(
                    [],
                    parent::getBaseRelations(),
                )
            ),
        ];
    }

    /**
     * Get the filters available for the resource.
     *
     * @return array
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
     * @param  array|null  $actionsIncludedInGroup
     * @return array
     */
    public static function getBulkActions(?array $actionsIncludedInGroup = []): array
    {
        return array_merge(
            parent::getBulkActions(),
            [],
        );
    }

    /**
     * Get the table actions available for the resource.
     *
     * @return array
     */
    public static function getTableActions(): array
    {
        return array_merge(
            parent::getTableActions(),
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
            'index' => ListExternalEntries::route('/'),
            'edit' => EditExternalEntry::route('/{record:entry_id}/edit'),
            'view' => ViewExternalEntry::route('/{record:entry_id}'),
        ];
    }
}
