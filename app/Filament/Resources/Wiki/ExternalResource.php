<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki;

use App\Enums\Models\Wiki\ResourceSite;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Fields\Select;
use App\Filament\Components\Filters\NumberFilter;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Components\Infolist\TimestampSection;
use App\Filament\RelationManagers\Wiki\ResourceRelationManager;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\ExternalResource\Pages\ListExternalResources;
use App\Filament\Resources\Wiki\ExternalResource\Pages\ViewExternalResource;
use App\Filament\Resources\Wiki\ExternalResource\RelationManagers\AnimeResourceRelationManager;
use App\Filament\Resources\Wiki\ExternalResource\RelationManagers\ArtistResourceRelationManager;
use App\Filament\Resources\Wiki\ExternalResource\RelationManagers\SongResourceRelationManager;
use App\Filament\Resources\Wiki\ExternalResource\RelationManagers\StudioResourceRelationManager;
use App\Models\Wiki\ExternalResource as ExternalResourceModel;
use App\Pivots\Wiki\AnimeResource;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ExternalResource.
 */
class ExternalResource extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Model>|null
     */
    protected static ?string $model = ExternalResourceModel::class;

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getLabel(): string
    {
        return __('filament.resources.singularLabel.external_resource');
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
        return __('filament.resources.label.external_resources');
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
        return __('filament-icons.resources.external_resources');
    }

    /**
     * Get the slug (URI key) for the resource.
     *
     * @return string
     */
    public static function getRecordSlug(): string
    {
        return 'external-resources';
    }

    /**
     * Get the title attribute for the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getRecordTitleAttribute(): string
    {
        return ExternalResourceModel::ATTRIBUTE_LINK;
    }

    /**
     * The form to the actions.
     *
     * @param  Schema  $schema
     * @return Schema
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make(ExternalResourceModel::ATTRIBUTE_SITE)
                    ->label(__('filament.fields.external_resource.site.name'))
                    ->helperText(__('filament.fields.external_resource.site.help'))
                    ->options(ResourceSite::class)
                    ->required(),

                TextInput::make(ExternalResourceModel::ATTRIBUTE_LINK)
                    ->label(__('filament.fields.external_resource.link.name'))
                    ->helperText(__('filament.fields.external_resource.link.help'))
                    ->required()
                    ->live()
                    ->partiallyRenderComponentsAfterStateUpdated([ExternalResourceModel::ATTRIBUTE_SITE, ExternalResourceModel::ATTRIBUTE_EXTERNAL_ID])
                    ->afterStateUpdated(function (Set $set, ?string $state) {
                        if ($state !== null) {
                            $set(ExternalResourceModel::ATTRIBUTE_SITE, ResourceSite::valueOf($state) ?? ResourceSite::OFFICIAL_SITE);
                            $set(ExternalResourceModel::ATTRIBUTE_EXTERNAL_ID, ResourceSite::parseIdFromLink($state));
                        }
                    }),

                TextInput::make(ExternalResourceModel::ATTRIBUTE_EXTERNAL_ID)
                    ->label(__('filament.fields.external_resource.external_id.name'))
                    ->helperText(__('filament.fields.external_resource.external_id.help'))
                    ->integer(),
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
                TextColumn::make(ExternalResourceModel::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id')),

                TextColumn::make(ExternalResourceModel::ATTRIBUTE_SITE)
                    ->label(__('filament.fields.external_resource.site.name'))
                    ->formatStateUsing(fn (ResourceSite $state) => $state->localize()),

                TextColumn::make(ExternalResourceModel::ATTRIBUTE_LINK)
                    ->label(__('filament.fields.external_resource.link.name'))
                    ->searchable()
                    ->copyableWithMessage(),

                TextColumn::make(ExternalResourceModel::ATTRIBUTE_EXTERNAL_ID)
                    ->label(__('filament.fields.external_resource.external_id.name')),

                TextColumn::make(AnimeResource::ATTRIBUTE_AS)
                    ->label(__('filament.fields.anime.resources.as.name'))
                    ->visibleOn(ResourceRelationManager::class),
            ]);
    }

    /**
     * Get the infolist available for the resource.
     *
     * @param  Schema  $schema
     * @return Schema
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(static::getRecordTitle($schema->getRecord()))
                    ->schema([
                        TextEntry::make(ExternalResourceModel::ATTRIBUTE_SITE)
                            ->label(__('filament.fields.external_resource.site.name'))
                            ->formatStateUsing(fn (ResourceSite $state) => $state->localize()),

                        TextEntry::make(ExternalResourceModel::ATTRIBUTE_LINK)
                            ->label(__('filament.fields.external_resource.link.name'))
                            ->copyableWithMessage(),

                        TextEntry::make(ExternalResourceModel::ATTRIBUTE_EXTERNAL_ID)
                            ->label(__('filament.fields.external_resource.external_id.name')),
                    ])
                    ->columns(3),

                TimestampSection::make(),
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
            RelationGroup::make(static::getLabel(), [
                AnimeResourceRelationManager::class,
                ArtistResourceRelationManager::class,
                SongResourceRelationManager::class,
                StudioResourceRelationManager::class,

                ...parent::getBaseRelations(),
            ]),
        ];
    }

    /**
     * Get the filters available for the resource.
     *
     * @return array
     */
    public static function getFilters(): array
    {
        return [
            SelectFilter::make(ExternalResourceModel::ATTRIBUTE_SITE)
                ->label(__('filament.fields.external_resource.site.name'))
                ->options(ResourceSite::class),

            NumberFilter::make(ExternalResourceModel::ATTRIBUTE_EXTERNAL_ID)
                ->label(__('filament.fields.external_resource.external_id.name')),

            ...parent::getFilters(),
        ];
    }

    /**
     * Get the actions available for the resource.
     *
     * @return array
     */
    public static function getRecordActions(): array
    {
        return [];
    }

    /**
     * Get the bulk actions available for the resource.
     *
     * @param  array|null  $actionsIncludedInGroup
     * @return array
     */
    public static function getBulkActions(?array $actionsIncludedInGroup = []): array
    {
        return [
            ...parent::getBulkActions(),
        ];
    }

    /**
     * Get the table actions available for the resource.
     *
     * @return array
     */
    public static function getTableActions(): array
    {
        return [
            ...parent::getTableActions(),
        ];
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
            'index' => ListExternalResources::route('/'),
            'view' => ViewExternalResource::route('/{record:resource_id}'),
        ];
    }
}
