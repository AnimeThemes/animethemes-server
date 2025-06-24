<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Song\Performance\Schemas;

use App\Filament\Components\Fields\BelongsTo;
use App\Filament\Resources\Wiki\Artist as ArtistResource;
use App\Filament\Resources\Wiki\Artist\RelationManagers\GroupPerformanceArtistRelationManager;
use App\Filament\Resources\Wiki\Artist\RelationManagers\PerformanceArtistRelationManager;
use App\Filament\Resources\Wiki\Song;
use App\Filament\Resources\Wiki\Song\RelationManagers\PerformanceSongRelationManager;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Song as SongModel;
use App\Models\Wiki\Song\Membership;
use App\Models\Wiki\Song\Performance;
use App\Pivots\Wiki\ArtistMember;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Arr;

/**
 * Class PerformanceForm.
 */
class PerformanceForm
{
    /**
     * Configure the form schema.
     *
     * @param  Schema  $schema
     * @return Schema
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                BelongsTo::make(Performance::ATTRIBUTE_SONG)
                    ->resource(Song::class)
                    ->required()
                    ->hiddenOn([PerformanceSongRelationManager::class])
                    ->disabledOn('edit')
                    ->columnSpanFull(),

                ...static::performancesFields(),
            ])
            ->columns(2);
    }

    /**
     * Get the performance fields to create a performance.
     *
     * @return array
     */
    public static function performancesFields(): array
    {
        return [
            Repeater::make(SongModel::RELATION_PERFORMANCES)
                ->label(__('filament.resources.label.artists'))
                ->addActionLabel(__('filament.buttons.add').' '.__('filament.resources.singularLabel.artist'))
                ->hiddenOn([PerformanceArtistRelationManager::class, GroupPerformanceArtistRelationManager::class])
                ->live(true)
                ->key('song.performances')
                ->collapsible()
                ->defaultItems(0)
                ->columns(3)
                ->columnSpanFull()
                ->formatStateUsing(function ($livewire, Get $get) {
                    /** @var SongModel|null $song */
                    $song = $livewire instanceof PerformanceSongRelationManager
                        ? $livewire->getOwnerRecord()
                        : SongModel::find($get(Performance::ATTRIBUTE_SONG));

                    return PerformanceSongRelationManager::formatArtists($song);
                })
                ->schema([
                    BelongsTo::make(Artist::ATTRIBUTE_ID)
                        ->resource(ArtistResource::class)
                        ->showCreateOption()
                        ->required()
                        ->hintAction(
                            Action::make('load')
                                ->label(__('filament.fields.performance.load_members.name'))
                                ->action(function (Get $get, Set $set) {
                                    $artistId = $get(Artist::ATTRIBUTE_ID);
                                    if ($artistId === null) {
                                        $set('memberships', []);
                                        return;
                                    }

                                    /** @var Artist $group */
                                    $group = Artist::query()
                                        ->with([Artist::RELATION_MEMBERS])
                                        ->find($artistId);

                                    $set('memberships', $group->members->map(fn (Artist $member) => [
                                        Membership::ATTRIBUTE_MEMBER => $member->getKey(),
                                        Membership::ATTRIBUTE_ALIAS => Arr::get($member->{$group->members()->getPivotAccessor()}, ArtistMember::ATTRIBUTE_ALIAS),
                                        Membership::ATTRIBUTE_AS => Arr::get($member->{$group->members()->getPivotAccessor()}, ArtistMember::ATTRIBUTE_AS),
                                    ])->toArray());
                                })
                        ),

                    TextInput::make(Performance::ATTRIBUTE_AS)
                        ->label(__('filament.fields.performance.as.name'))
                        ->helperText(__('filament.fields.performance.as.help')),

                    TextInput::make(Performance::ATTRIBUTE_ALIAS)
                        ->label(__('filament.fields.performance.alias.name'))
                        ->helperText(__('filament.fields.performance.alias.help')),

                    Repeater::make('memberships')
                        ->label(__('filament.resources.label.memberships'))
                        ->helperText(__('filament.fields.performance.memberships.help'))
                        ->addActionLabel(__('filament.fields.performance.memberships.add'))
                        ->collapsible()
                        ->defaultItems(0)
                        ->columns(3)
                        ->columnSpanFull()
                        ->schema([
                            BelongsTo::make(Membership::ATTRIBUTE_MEMBER)
                                ->resource(ArtistResource::class)
                                ->showCreateOption()
                                ->label(__('filament.fields.membership.member'))
                                ->required(),

                            TextInput::make(Membership::ATTRIBUTE_AS)
                                ->label(__('filament.fields.membership.as.name'))
                                ->helperText(__('filament.fields.membership.as.help')),

                            TextInput::make(Membership::ATTRIBUTE_ALIAS)
                                ->label(__('filament.fields.membership.alias.name'))
                                ->helperText(__('filament.fields.membership.alias.help')),
                        ]),
                ])
                ->saveRelationshipsUsing(function (Get $get, ?array $state) {
                    $song = SongModel::find($get(Performance::ATTRIBUTE_SONG));
                    PerformanceSongRelationManager::saveArtists($song, $state);
                })
        ];
    }
}
