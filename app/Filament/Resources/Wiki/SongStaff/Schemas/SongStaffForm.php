<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\SongStaff\Schemas;

use App\Filament\Actions\Models\Wiki\SongStaff\LoadMembersAction;
use App\Filament\Components\Fields\BelongsTo;
use App\Filament\Components\Fields\TextInput;
use App\Filament\Resources\Wiki\Artist\RelationManagers\MemberSongStaffArtistRelationManager;
use App\Filament\Resources\Wiki\Artist\RelationManagers\SongStaffArtistRelationManager;
use App\Filament\Resources\Wiki\ArtistResource;
use App\Filament\Resources\Wiki\Song\RelationManagers\SongStaffSongRelationManager;
use App\Filament\Resources\Wiki\SongResource;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Song;
use App\Models\Wiki\SongStaff;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class SongStaffForm
{
    final public const string REPEATER_STAFF = 'song_staff';

    final public const string REPEATER_MEMBERS = 'members';

    /**
     * Configure the form schema.
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                BelongsTo::make(SongStaff::ATTRIBUTE_SONG)
                    ->resource(SongResource::class)
                    ->required()
                    ->hiddenOn([SongStaffSongRelationManager::class])
                    ->disabledOn('edit')
                    ->columnSpanFull(),

                ...static::songStaffFields(),
            ])
            ->columns(2);
    }

    /**
     * Get the song staff fields to create a song staff.
     *
     * @return \Filament\Schemas\Components\Component[]
     */
    public static function songStaffFields(): array
    {
        return [
            Repeater::make(self::REPEATER_STAFF)
                ->label(__('filament.resources.label.staff'))
                ->addActionLabel(__('filament.buttons.add', ['label' => __('filament.resources.singularLabel.artist')]))
                ->hiddenOn([SongStaffArtistRelationManager::class, MemberSongStaffArtistRelationManager::class])
                ->live(true)
                ->key('song.staff')
                ->collapsible()
                ->defaultItems(1)
                ->columns(4)
                ->columnSpanFull()
                ->reorderableWithButtons()
                ->formatStateUsing(function ($livewire, Get $get, ?array $state): array {
                    /** @var Song|null $song */
                    $song = $livewire instanceof SongStaffSongRelationManager
                        ? $livewire->getOwnerRecord()
                        : Song::query()->find($get(SongStaff::ATTRIBUTE_SONG));

                    $artists = SongStaffSongRelationManager::formatArtists($song);

                    return blank($artists) ? ($state ?? []) : $artists;
                })
                ->schema([
                    BelongsTo::make(Artist::ATTRIBUTE_ID)
                        ->resource(ArtistResource::class)
                        ->showCreateOption()
                        ->required()
                        ->hintAction(LoadMembersAction::make()),

                    TextInput::make(SongStaff::ATTRIBUTE_ROLE)
                        ->label(__('filament.fields.song_staff.role.name'))
                        ->helperText(__('filament.fields.song_staff.role.help'))
                        ->required()
                        ->datalist(SongStaff::$roles),

                    TextInput::make(SongStaff::ATTRIBUTE_AS)
                        ->label(__('filament.fields.song_staff.as.name'))
                        ->helperText(__('filament.fields.song_staff.as.help')),

                    TextInput::make(SongStaff::ATTRIBUTE_ALIAS)
                        ->label(__('filament.fields.song_staff.alias.name'))
                        ->helperText(__('filament.fields.song_staff.alias.help')),

                    Repeater::make(self::REPEATER_MEMBERS)
                        ->label(__('filament.resources.label.members'))
                        ->helperText(__('filament.fields.song_staff.members.help'))
                        ->addActionLabel(__('filament.buttons.add', ['label' => __('filament.resources.singularLabel.member')]))
                        ->collapsible()
                        ->defaultItems(0)
                        ->columns(3)
                        ->columnSpanFull()
                        ->reorderableWithButtons()
                        ->schema([
                            BelongsTo::make(SongStaff::ATTRIBUTE_MEMBER)
                                ->resource(ArtistResource::class)
                                ->showCreateOption()
                                ->label(__('filament.fields.song_staff.member'))
                                ->required(),

                            TextInput::make(SongStaff::ATTRIBUTE_MEMBER_AS)
                                ->label(__('filament.fields.song_staff.member_as.name'))
                                ->helperText(__('filament.fields.song_staff.member_as.help')),

                            TextInput::make(SongStaff::ATTRIBUTE_MEMBER_ALIAS)
                                ->label(__('filament.fields.song_staff.member_alias.name'))
                                ->helperText(__('filament.fields.song_staff.member_alias.help')),
                        ]),
                ])
                ->saveRelationshipsUsing(fn (Get $get, ?array $state) => SongStaffSongRelationManager::saveArtists($get(SongStaff::ATTRIBUTE_SONG), $state)),
        ];
    }
}
