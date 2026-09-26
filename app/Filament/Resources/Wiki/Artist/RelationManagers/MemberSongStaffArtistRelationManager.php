<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Artist\RelationManagers;

use App\Actions\Models\Wiki\Song\ManageSongStaff;
use App\Filament\Actions\Base\CreateAction;
use App\Filament\Components\Fields\BelongsTo;
use App\Filament\Components\Fields\TextInput;
use App\Filament\RelationManagers\Wiki\SongStaffRelationManager;
use App\Filament\Resources\Wiki\ArtistResource;
use App\Models\Wiki\Artist;
use App\Models\Wiki\SongStaff;
use Filament\Actions\Action;
use Filament\Schemas\Components\Component;
use Filament\Tables\Table;

class MemberSongStaffArtistRelationManager extends SongStaffRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     */
    protected static string $relationship = Artist::RELATION_MEMBER_SONG_STAFF;

    /**
     * @return Component[]
     */
    public function getPivotComponents(): array
    {
        return [
            BelongsTo::make(SongStaff::ATTRIBUTE_ARTIST)
                ->resource(ArtistResource::class)
                ->label(__('filament.fields.song_staff.group'))
                ->required()
                ->columnSpanFull(),

            TextInput::make(SongStaff::ATTRIBUTE_ROLE)
                ->label(__('filament.fields.song_staff.role.name'))
                ->helperText(__('filament.fields.song_staff.role.help'))
                ->required()
                ->columnSpanFull()
                ->datalist(SongStaff::$roles),

            TextInput::make(SongStaff::ATTRIBUTE_AS)
                ->label(__('filament.fields.song_staff.as.name'))
                ->helperText(__('filament.fields.song_staff.as.help')),

            TextInput::make(SongStaff::ATTRIBUTE_ALIAS)
                ->label(__('filament.fields.song_staff.alias.name'))
                ->helperText(__('filament.fields.song_staff.alias.help')),

            TextInput::make(SongStaff::ATTRIBUTE_MEMBER_AS)
                ->label(__('filament.fields.song_staff.member_as.name'))
                ->helperText(__('filament.fields.song_staff.member_as.help')),

            TextInput::make(SongStaff::ATTRIBUTE_MEMBER_ALIAS)
                ->label(__('filament.fields.song_staff.member_alias.name'))
                ->helperText(__('filament.fields.song_staff.member_alias.help')),
        ];
    }

    public function table(Table $table): Table
    {
        return parent::table($table)
            ->heading(__('filament.resources.label.member_song_staff'))
            ->modelLabel(__('filament.resources.singularLabel.member_song_staff'));
    }

    /**
     * Get the header actions available for the relation.
     *
     * @return array<int, Action>
     */
    public static function getHeaderActions(): array
    {
        return [
            CreateAction::make('new-song-staff')
                ->after(function (SongStaff $record): void {
                    $record->moveToEnd();
                    ManageSongStaff::setArtistMemberRelevance();
                }),
        ];
    }
}
