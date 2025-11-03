<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki;

use App\Enums\Filament\NavigationGroup;
use App\Enums\Models\Wiki\ResourceSite;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Fields\Select;
use App\Filament\Components\Fields\TextInput;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Components\Infolist\TimestampSection;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\ExternalResource\Pages\ListExternalResources;
use App\Filament\Resources\Wiki\ExternalResource\Pages\ViewExternalResource;
use App\Filament\Resources\Wiki\ExternalResource\RelationManagers\AnimeResourceRelationManager;
use App\Filament\Resources\Wiki\ExternalResource\RelationManagers\ArtistResourceRelationManager;
use App\Filament\Resources\Wiki\ExternalResource\RelationManagers\EntryResourceRelationManager;
use App\Filament\Resources\Wiki\ExternalResource\RelationManagers\SongResourceRelationManager;
use App\Filament\Resources\Wiki\ExternalResource\RelationManagers\StudioResourceRelationManager;
use App\Models\Wiki\ExternalResource as ExternalResourceModel;
use Filament\QueryBuilder\Constraints\NumberConstraint;
use Filament\QueryBuilder\Constraints\SelectConstraint;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ExternalResource extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Model>|null
     */
    protected static ?string $model = ExternalResourceModel::class;

    public static function getModelLabel(): string
    {
        return __('filament.resources.singularLabel.external_resource');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.label.external_resources');
    }

    public static function getNavigationGroup(): NavigationGroup
    {
        return NavigationGroup::CONTENT;
    }

    public static function getNavigationIcon(): Heroicon
    {
        return Heroicon::OutlinedLink;
    }

    public static function getRecordSlug(): string
    {
        return 'external-resources';
    }

    public static function getRecordTitleAttribute(): string
    {
        return ExternalResourceModel::ATTRIBUTE_LINK;
    }

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
                    ->uri()
                    ->partiallyRenderComponentsAfterStateUpdated([ExternalResourceModel::ATTRIBUTE_SITE, ExternalResourceModel::ATTRIBUTE_EXTERNAL_ID])
                    ->afterStateUpdated(function (Set $set, ?string $state): void {
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

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make(ExternalResourceModel::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id')),

                TextColumn::make(ExternalResourceModel::ATTRIBUTE_SITE)
                    ->label(__('filament.fields.external_resource.site.name'))
                    ->formatStateUsing(fn (ResourceSite $state): ?string => $state->localize()),

                TextColumn::make(ExternalResourceModel::ATTRIBUTE_LINK)
                    ->label(__('filament.fields.external_resource.link.name'))
                    ->searchable()
                    ->copyableWithMessage(),

                TextColumn::make(ExternalResourceModel::ATTRIBUTE_EXTERNAL_ID)
                    ->label(__('filament.fields.external_resource.external_id.name')),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(static::getRecordTitle($schema->getRecord()))
                    ->schema([
                        TextEntry::make(ExternalResourceModel::ATTRIBUTE_SITE)
                            ->label(__('filament.fields.external_resource.site.name'))
                            ->formatStateUsing(fn (ResourceSite $state): ?string => $state->localize()),

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
     * @return \Filament\Tables\Filters\BaseFilter[]
     */
    public static function getFilters(): array
    {
        return [
            QueryBuilder::make()
                ->constraints([
                    SelectConstraint::make(ExternalResourceModel::ATTRIBUTE_SITE)
                        ->label(__('filament.fields.external_resource.site.name'))
                        ->options(ResourceSite::class),

                    NumberConstraint::make(ExternalResourceModel::ATTRIBUTE_EXTERNAL_ID)
                        ->label(__('filament.fields.external_resource.external_id.name')),

                    ...parent::getConstraints(),
                ]),

            ...parent::getFilters(),
        ];
    }

    /**
     * @return array<int, RelationGroup|class-string<\Filament\Resources\RelationManagers\RelationManager>>
     */
    public static function getRelations(): array
    {
        return [
            RelationGroup::make(static::getModelLabel(), [
                AnimeResourceRelationManager::class,
                EntryResourceRelationManager::class,
                ArtistResourceRelationManager::class,
                SongResourceRelationManager::class,
                StudioResourceRelationManager::class,

                ...parent::getBaseRelations(),
            ]),
        ];
    }

    /**
     * @return array<string, \Filament\Resources\Pages\PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => ListExternalResources::route('/'),
            'view' => ViewExternalResource::route('/{record:resource_id}'),
        ];
    }
}
