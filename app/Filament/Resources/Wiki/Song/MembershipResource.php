<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Song;

use App\Enums\Filament\NavigationGroup;
use App\Filament\Components\Columns\BelongsToColumn;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Fields\BelongsTo;
use App\Filament\Components\Fields\TextInput;
use App\Filament\Components\Infolist\BelongsToEntry;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Components\Infolist\TimestampSection;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\ArtistResource;
use App\Filament\Resources\Wiki\Song\Membership\Pages\ListMemberships;
use App\Filament\Resources\Wiki\Song\Membership\Pages\ViewMembership;
use App\Models\Wiki\Song\Membership;
use Filament\QueryBuilder\Constraints\TextConstraint;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class MembershipResource extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Model>|null
     */
    protected static ?string $model = Membership::class;

    public static function getModelLabel(): string
    {
        return __('filament.resources.singularLabel.membership');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.label.memberships');
    }

    public static function getNavigationGroup(): NavigationGroup
    {
        return NavigationGroup::CONTENT;
    }

    public static function getNavigationIcon(): Heroicon
    {
        return Heroicon::OutlinedListBullet;
    }

    public static function getRecordTitleAttribute(): string
    {
        return Membership::ATTRIBUTE_ID;
    }

    public static function getRecordSlug(): string
    {
        return 'memberships';
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // Necessary to prevent lazy loading when loading related resources
        return $query->with([
            Membership::RELATION_GROUP,
            Membership::RELATION_MEMBER,
        ]);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                BelongsTo::make(Membership::ATTRIBUTE_ARTIST)
                    ->resource(ArtistResource::class)
                    ->required(),

                BelongsTo::make(Membership::ATTRIBUTE_MEMBER)
                    ->resource(ArtistResource::class)
                    ->label(__('filament.fields.membership.member'))
                    ->required(),

                TextInput::make(Membership::ATTRIBUTE_ALIAS)
                    ->label(__('filament.fields.membership.alias.name'))
                    ->helperText(__('filament.fields.membership.alias.help')),

                TextInput::make(Membership::ATTRIBUTE_AS)
                    ->label(__('filament.fields.membership.as.name'))
                    ->helperText(__('filament.fields.membership.as.help')),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make(Membership::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id')),

                BelongsToColumn::make(Membership::RELATION_GROUP, ArtistResource::class),

                BelongsToColumn::make(Membership::RELATION_MEMBER, ArtistResource::class)
                    ->label(__('filament.fields.membership.member')),

                TextColumn::make(Membership::ATTRIBUTE_ALIAS)
                    ->label(__('filament.fields.membership.alias.name')),

                TextColumn::make(Membership::ATTRIBUTE_AS)
                    ->label(__('filament.fields.membership.as.name')),
            ])
            ->searchable();
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(static::getRecordTitle($schema->getRecord()))
                    ->schema([
                        TextEntry::make(Membership::ATTRIBUTE_ID)
                            ->label(__('filament.fields.base.id')),

                        BelongsToEntry::make(Membership::RELATION_GROUP, ArtistResource::class),

                        BelongsToEntry::make(Membership::RELATION_MEMBER, ArtistResource::class)
                            ->label(__('filament.fields.membership.member')),

                        TextEntry::make(Membership::ATTRIBUTE_ALIAS)
                            ->label(__('filament.fields.membership.alias.name')),

                        TextEntry::make(Membership::ATTRIBUTE_AS)
                            ->label(__('filament.fields.membership.as.name')),
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
                    TextConstraint::make(Membership::ATTRIBUTE_ALIAS)
                        ->label(__('filament.fields.membership.alias.name')),

                    TextConstraint::make(Membership::ATTRIBUTE_AS)
                        ->label(__('filament.fields.membership.as.name')),

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
            'index' => ListMemberships::route('/'),
            'view' => ViewMembership::route('/{record:membership_id}'),
        ];
    }
}
