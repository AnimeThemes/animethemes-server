<?php

declare(strict_types=1);

namespace App\Filament\Resources\List;

use App\Enums\Models\List\ExternalProfileSite;
use App\Enums\Models\List\ExternalProfileVisibility;
use App\Filament\Components\Columns\BelongsToColumn;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Fields\BelongsTo;
use App\Filament\Components\Fields\Select;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Resources\Auth\User;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\List\External\Pages\CreateExternalProfile;
use App\Filament\Resources\List\External\Pages\EditExternalProfile;
use App\Filament\Resources\List\External\Pages\ListExternalProfiles;
use App\Filament\Resources\List\External\Pages\ViewExternalProfile;
use App\Filament\Resources\List\External\RelationManagers\ExternalEntryExternalProfileRelationManager;
use App\Models\Auth\User as UserModel;
use App\Models\List\ExternalProfile as ExternalProfileModel;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Infolist;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Tables\Table;
use Illuminate\Validation\Rules\Enum;

/**
 * Class ExternalProfile.
 */
class ExternalProfile extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string|null
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
        return __('filament.resources.icon.external_profiles');
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
        return 'external-profiles';
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
                BelongsTo::make(ExternalProfileModel::ATTRIBUTE_USER)
                    ->resource(User::class),

                TextInput::make(ExternalProfileModel::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.external_profile.name.name'))
                    ->helperText(__('filament.fields.external_profile.name.help'))
                    ->required()
                    ->maxLength(192)
                    ->rules(['required', 'max:192']),

                Select::make(ExternalProfileModel::ATTRIBUTE_SITE)
                    ->label(__('filament.fields.external_profile.site.name'))
                    ->helperText(__('filament.fields.external_profile.site.help'))
                    ->options(ExternalProfileSite::asSelectArray())
                    ->required()
                    ->rules(['required', new Enum(ExternalProfileSite::class)]),

                Select::make(ExternalProfileModel::ATTRIBUTE_VISIBILITY)
                    ->label(__('filament.fields.external_profile.visibility.name'))
                    ->helperText(__('filament.fields.external_profile.visibility.help'))
                    ->options(ExternalProfileVisibility::asSelectArray())
                    ->required()
                    ->rules(['required', new Enum(ExternalProfileVisibility::class)]),
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
                BelongsToColumn::make(ExternalProfileModel::RELATION_USER.'.'.UserModel::ATTRIBUTE_NAME)
                    ->resource(User::class)
                    ->sortable()
                    ->toggleable(),

                TextColumn::make(ExternalProfileModel::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.external_profile.name.name'))
                    ->sortable()
                    ->toggleable(),

                TextColumn::make(ExternalProfileModel::ATTRIBUTE_SITE)
                    ->label(__('filament.fields.external_profile.site.name'))
                    ->formatStateUsing(fn (ExternalProfileSite $state) => $state->localize())
                    ->sortable()
                    ->toggleable(),

                TextColumn::make(ExternalProfileModel::ATTRIBUTE_VISIBILITY)
                    ->label(__('filament.fields.external_profile.visibility.name'))
                    ->formatStateUsing(fn (ExternalProfileVisibility $state) => $state->localize())
                    ->sortable()
                    ->toggleable(),

                TextColumn::make(ExternalProfileModel::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id'))
                    ->sortable()
                    ->toggleable(),
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
                        TextEntry::make(ExternalProfileModel::ATTRIBUTE_USER)
                            ->label(__('filament.resources.singularLabel.user'))
                            ->urlToRelated(User::class, ExternalProfileModel::RELATION_USER, true)
                            ->placeholder('-'),

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
                    [
                        ExternalEntryExternalProfileRelationManager::class,
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
            'index' => ListExternalProfiles::route('/'),
            'create' => CreateExternalProfile::route('/create'),
            'view' => ViewExternalProfile::route('/{record:profile_id}'),
            'edit' => EditExternalProfile::route('/{record:profile_id}/edit'),
        ];
    }
}
