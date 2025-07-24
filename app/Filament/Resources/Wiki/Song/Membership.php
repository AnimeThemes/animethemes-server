<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Song;

use App\Filament\Components\Columns\BelongsToColumn;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Fields\BelongsTo;
use App\Filament\Components\Fields\TextInput;
use App\Filament\Components\Infolist\BelongsToEntry;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Components\Infolist\TimestampSection;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\Artist as ArtistResource;
use App\Filament\Resources\Wiki\Song\Membership\Pages\ListMemberships;
use App\Filament\Resources\Wiki\Song\Membership\Pages\ViewMembership;
use App\Models\Wiki\Song\Membership as MembershipModel;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Membership extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Model>|null
     */
    protected static ?string $model = MembershipModel::class;

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getModelLabel(): string
    {
        return __('filament.resources.singularLabel.membership');
    }

    /**
     * Get the displayable label of the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.label.memberships');
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
        return __('filament-icons.resources.memberships');
    }

    /**
     * Get the title attribute for the resource.
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getRecordTitleAttribute(): string
    {
        return MembershipModel::ATTRIBUTE_ID;
    }

    /**
     * Get the slug (URI key) for the resource.
     */
    public static function getRecordSlug(): string
    {
        return 'memberships';
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
        return $query->with([
            MembershipModel::RELATION_ARTIST,
            MembershipModel::RELATION_MEMBER,
        ]);
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
                BelongsTo::make(MembershipModel::ATTRIBUTE_ARTIST)
                    ->resource(ArtistResource::class)
                    ->required(),

                BelongsTo::make(MembershipModel::ATTRIBUTE_MEMBER)
                    ->resource(ArtistResource::class)
                    ->label(__('filament.fields.membership.member'))
                    ->required(),

                TextInput::make(MembershipModel::ATTRIBUTE_ALIAS)
                    ->label(__('filament.fields.membership.alias.name'))
                    ->helperText(__('filament.fields.membership.alias.help')),

                TextInput::make(MembershipModel::ATTRIBUTE_AS)
                    ->label(__('filament.fields.membership.as.name'))
                    ->helperText(__('filament.fields.membership.as.help')),
            ])
            ->columns(2);
    }

    /**
     * The index page of the resource.
     */
    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make(MembershipModel::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id')),

                BelongsToColumn::make(MembershipModel::RELATION_ARTIST, ArtistResource::class),

                BelongsToColumn::make(MembershipModel::RELATION_MEMBER, ArtistResource::class)
                    ->label(__('filament.fields.membership.member')),

                TextColumn::make(MembershipModel::ATTRIBUTE_ALIAS)
                    ->label(__('filament.fields.membership.alias.name')),

                TextColumn::make(MembershipModel::ATTRIBUTE_AS)
                    ->label(__('filament.fields.membership.as.name')),
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
                        TextEntry::make(MembershipModel::ATTRIBUTE_ID)
                            ->label(__('filament.fields.base.id')),

                        BelongsToEntry::make(MembershipModel::RELATION_ARTIST, ArtistResource::class),

                        BelongsToEntry::make(MembershipModel::RELATION_MEMBER, ArtistResource::class)
                            ->label(__('filament.fields.membership.member')),

                        TextEntry::make(MembershipModel::ATTRIBUTE_ALIAS)
                            ->label(__('filament.fields.membership.alias.name')),

                        TextEntry::make(MembershipModel::ATTRIBUTE_AS)
                            ->label(__('filament.fields.membership.as.name')),
                    ])
                    ->columns(3),

                TimestampSection::make(),
            ]);
    }

    /**
     * Get the relationships available for the resource.
     *
     * @return array<int, RelationGroup|class-string<\Filament\Resources\RelationManagers\RelationManager>>
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getRelations(): array
    {
        return [
            RelationGroup::make(static::getModelLabel(), [
                ...parent::getBaseRelations(),
            ]),
        ];
    }

    /**
     * Get the pages available for the resource.
     *
     * @return array<string, \Filament\Resources\Pages\PageRegistration>
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getPages(): array
    {
        return [
            'index' => ListMemberships::route('/'),
            'view' => ViewMembership::route('/{record:membership_id}'),
        ];
    }
}
