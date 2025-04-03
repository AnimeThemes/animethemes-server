<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Artist\RelationManagers;

use App\Filament\RelationManagers\Wiki\ArtistRelationManager;
use App\Models\Wiki\Artist;
use App\Pivots\Wiki\ArtistMember;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Table;

/**
 * Class MemberArtistRelationManager.
 */
class MemberArtistRelationManager extends ArtistRelationManager
{
    /**
     * Get the pivot fields of the relation.
     *
     * @return array<int, Component>
     */
    public function getPivotFields(): array
    {
        return [
            TextInput::make(ArtistMember::ATTRIBUTE_AS)
                ->label(__('filament.fields.artist.members.as.name'))
                ->helperText(__('filament.fields.artist.members.as.help')),

            TextInput::make(ArtistMember::ATTRIBUTE_ALIAS)
                ->label(__('filament.fields.artist.members.alias.name'))
                ->helperText(__('filament.fields.artist.members.alias.help')),

            TextInput::make(ArtistMember::ATTRIBUTE_NOTES)
                ->label(__('filament.fields.artist.members.notes.name'))
                ->helperText(__('filament.fields.artist.members.notes.help')),
        ];
    }

    /**
     * The relationship the relation manager corresponds to.
     *
     * @var string
     */
    protected static string $relationship = Artist::RELATION_MEMBERS;

    /**
     * The index page of the resource.
     *
     * @param  Table  $table
     * @return Table
     */
    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->inverseRelationship(Artist::RELATION_GROUPS)
        )
            ->heading(__('filament.resources.label.members'))
            ->modelLabel(__('filament.resources.singularLabel.member'));
    }

    /**
     * Get the filters available for the relation.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getFilters(): array
    {
        return array_merge(
            parent::getFilters(),
            [],
        );
    }

    /**
     * Get the actions available for the relation.
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
     * Get the bulk actions available for the relation.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getBulkActions(?array $actionsIncludedInGroup = []): array
    {
        return array_merge(
            parent::getBulkActions(),
            [],
        );
    }

    /**
     * Get the header actions available for the relation. These are merged with the table actions of the resources.
     *
     * @return array
     */
    public static function getHeaderActions(): array
    {
        return array_merge(
            parent::getHeaderActions(),
            [],
        );
    }
}
