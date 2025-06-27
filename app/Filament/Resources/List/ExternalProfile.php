<?php

declare(strict_types=1);

namespace App\Filament\Resources\List;

use App\Enums\Models\List\ExternalProfileSite;
use App\Enums\Models\List\ExternalProfileVisibility;
use App\Filament\Actions\Models\List\External\SyncExternalProfileAction;
use App\Filament\Components\Columns\BelongsToColumn;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Fields\BelongsTo;
use App\Filament\Components\Fields\Select;
use App\Filament\Components\Infolist\BelongsToEntry;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Components\Infolist\TimestampSection;
use App\Filament\Resources\Auth\User;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\List\External\Pages\ListExternalProfiles;
use App\Filament\Resources\List\External\Pages\ViewExternalProfile;
use App\Filament\Resources\List\External\RelationManagers\ExternalEntryExternalProfileRelationManager;
use App\Models\List\ExternalProfile as ExternalProfileModel;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ExternalProfile.
 */
class ExternalProfile extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Model>|null
     */
    protected static ?string $model = ExternalProfileModel::class;

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getLabel(): string
    {
        return __('filament.resources.singularLabel.external_profile');
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
        return __('filament.resources.label.external_profiles');
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
        return __('filament-icons.resources.external_profiles');
    }

    /**
     * Get the slug (URI key) for the resource.
     *
     * @return string
     */
    public static function getRecordSlug(): string
    {
        return 'external-profiles';
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
        return ExternalProfileModel::ATTRIBUTE_NAME;
    }

    /**
     * Get the eloquent query for the resource.
     *
     * @return Builder
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // Necessary to prevent lazy loading when loading related resources
        return $query->with([ExternalProfileModel::RELATION_USER]);
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
                BelongsTo::make(ExternalProfileModel::ATTRIBUTE_USER)
                    ->resource(User::class),

                TextInput::make(ExternalProfileModel::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.external_profile.name.name'))
                    ->helperText(__('filament.fields.external_profile.name.help'))
                    ->required()
                    ->maxLength(192),

                Select::make(ExternalProfileModel::ATTRIBUTE_SITE)
                    ->label(__('filament.fields.external_profile.site.name'))
                    ->helperText(__('filament.fields.external_profile.site.help'))
                    ->options(ExternalProfileSite::class)
                    ->required(),

                Select::make(ExternalProfileModel::ATTRIBUTE_VISIBILITY)
                    ->label(__('filament.fields.external_profile.visibility.name'))
                    ->helperText(__('filament.fields.external_profile.visibility.help'))
                    ->options(ExternalProfileVisibility::class)
                    ->required(),
            ])
            ->columns(2);
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
                BelongsToColumn::make(ExternalProfileModel::RELATION_USER, User::class),

                TextColumn::make(ExternalProfileModel::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.external_profile.name.name')),

                TextColumn::make(ExternalProfileModel::ATTRIBUTE_SITE)
                    ->label(__('filament.fields.external_profile.site.name'))
                    ->formatStateUsing(fn (ExternalProfileSite $state) => $state->localize()),

                TextColumn::make(ExternalProfileModel::ATTRIBUTE_VISIBILITY)
                    ->label(__('filament.fields.external_profile.visibility.name'))
                    ->formatStateUsing(fn (ExternalProfileVisibility $state) => $state->localize()),

                TextColumn::make(ExternalProfileModel::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id')),
            ])
            ->searchable();
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
                        BelongsToEntry::make(ExternalProfileModel::RELATION_USER, User::class, true),

                        TextEntry::make(ExternalProfileModel::ATTRIBUTE_NAME)
                            ->label(__('filament.fields.external_profile.name.name')),

                        TextEntry::make(ExternalProfileModel::ATTRIBUTE_SITE)
                            ->label(__('filament.fields.external_profile.site.name'))
                            ->formatStateUsing(fn (ExternalProfileSite $state) => $state->localize()),

                        TextEntry::make(ExternalProfileModel::ATTRIBUTE_VISIBILITY)
                            ->label(__('filament.fields.external_profile.visibility.name'))
                            ->formatStateUsing(fn (ExternalProfileVisibility $state) => $state->localize()),

                        TextEntry::make(ExternalProfileModel::ATTRIBUTE_ID)
                            ->label(__('filament.fields.base.id')),
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
                ExternalEntryExternalProfileRelationManager::class,

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
        return [
            SyncExternalProfileAction::make('sync-profile'),
        ];
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
            'index' => ListExternalProfiles::route('/'),
            'view' => ViewExternalProfile::route('/{record:profile_id}'),
        ];
    }
}
