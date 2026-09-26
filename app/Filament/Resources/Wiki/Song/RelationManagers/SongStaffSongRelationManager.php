<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Song\RelationManagers;

use App\Actions\Models\Wiki\Song\ManageSongStaff;
use App\Filament\RelationManagers\Wiki\SongStaffRelationManager;
use App\Filament\Resources\Wiki\SongStaff\Schemas\SongStaffForm;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Song;
use App\Models\Wiki\SongStaff;
use Filament\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class SongStaffSongRelationManager extends SongStaffRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     */
    protected static string $relationship = Song::RELATION_STAFF;

    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->inverseRelationship(SongStaff::RELATION_SONG)
        )
            ->heading(__('filament.resources.label.staff'))
            ->reorderable(SongStaff::ATTRIBUTE_RELEVANCE)
            ->defaultSort(SongStaff::ATTRIBUTE_RELEVANCE);
    }

    /**
     * Get the header actions available for the relation.
     *
     * @return array<int, Action>
     */
    public static function getHeaderActions(): array
    {
        return [
            Action::make('manage-song-staff')
                ->label(__('filament.actions.song_staff.manage_staff'))
                ->action(fn ($livewire, array $data) => static::saveArtists($livewire->getOwnerRecord(), Arr::get($data, SongStaffForm::REPEATER_STAFF)))
                ->schema(SongStaffForm::songStaffFields()),
        ];
    }

    /**
     * Format artists to the action.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function formatArtists(?Song $song = null): array
    {
        if (! ($song instanceof Song)) {
            return [];
        }

        $song->load(Song::RELATION_STAFF);

        return $song->staff
            ->sortBy(SongStaff::ATTRIBUTE_RELEVANCE)
            ->groupBy(SongStaff::ATTRIBUTE_ARTIST)
            /** @phpstan-ignore-next-line */
            ->mapWithKeys(fn (Collection $staffs, $artistId): array => [
                Str::uuid()->__toString() => [
                    SongStaff::ATTRIBUTE_ARTIST => $artistId,
                    SongStaff::ATTRIBUTE_ROLE => $staffs->first()->getAttribute(SongStaff::ATTRIBUTE_ROLE),
                    SongStaff::ATTRIBUTE_AS => $staffs->first()->getAttribute(SongStaff::ATTRIBUTE_AS),
                    SongStaff::ATTRIBUTE_ALIAS => $staffs->first()->getAttribute(SongStaff::ATTRIBUTE_ALIAS),
                    SongStaffForm::REPEATER_MEMBERS => $staffs
                        /** @phpstan-ignore-next-line */
                        ->filter(fn (SongStaff $staff): bool => filled($staff->getAttribute(SongStaff::ATTRIBUTE_MEMBER)))
                        /** @phpstan-ignore-next-line */
                        ->mapWithKeys(fn (SongStaff $staff): array => [
                            Str::uuid()->__toString() => [
                                SongStaff::ATTRIBUTE_MEMBER => $staff->getAttribute(SongStaff::ATTRIBUTE_MEMBER),
                                SongStaff::ATTRIBUTE_MEMBER_ALIAS => $staff->getAttribute(SongStaff::ATTRIBUTE_MEMBER_ALIAS),
                                SongStaff::ATTRIBUTE_MEMBER_AS => $staff->getAttribute(SongStaff::ATTRIBUTE_MEMBER_AS),
                            ],
                        ])->all(),
                ],
            ])
            ->all();
    }

    /**
     * Save the artists to the action.
     */
    public static function saveArtists(Song|int|null $song = null, array $staffs = []): void
    {
        if (is_null($song) || blank($staffs)) {
            return;
        }

        $action = new ManageSongStaff($song);

        foreach ($staffs as $staff) {
            $artist = intval(Arr::get($staff, Artist::ATTRIBUTE_ID));
            $role = Arr::get($staff, SongStaff::ATTRIBUTE_ROLE);
            $artistAlias = Arr::get($staff, SongStaff::ATTRIBUTE_ALIAS);
            $artistAs = Arr::get($staff, SongStaff::ATTRIBUTE_AS);

            if (blank(Arr::get($staff, SongStaffForm::REPEATER_MEMBERS))) {
                $action->addArtist(
                    $role,
                    $artist,
                    alias: $artistAlias,
                    as: $artistAs,
                );
                continue;
            }

            foreach (Arr::get($staff, SongStaffForm::REPEATER_MEMBERS) as $member) {
                $action->addArtist(
                    $role,
                    $artist,
                    intval(Arr::get($member, SongStaff::ATTRIBUTE_MEMBER)),
                    $artistAlias,
                    $artistAs,
                    Arr::get($member, SongStaff::ATTRIBUTE_MEMBER_ALIAS),
                    Arr::get($member, SongStaff::ATTRIBUTE_MEMBER_AS),
                );
            }
        }

        $action->commit();
    }
}
