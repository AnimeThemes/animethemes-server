<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Song\RelationManagers;

use App\Actions\Models\Wiki\Song\ManageSongPerformances;
use App\Filament\RelationManagers\Wiki\Song\PerformanceRelationManager;
use App\Filament\Resources\Wiki\Song\Performance\Schemas\PerformanceForm;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Song;
use App\Models\Wiki\Song\Membership;
use App\Models\Wiki\Song\Performance;
use Filament\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Support\Arr;

class PerformanceSongRelationManager extends PerformanceRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     */
    protected static string $relationship = Song::RELATION_PERFORMANCES;

    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->inverseRelationship(Performance::RELATION_SONG)
        );
    }

    /**
     * Get the header actions available for the relation.
     *
     * @return array<int, Action>
     */
    public static function getHeaderActions(): array
    {
        return [
            Action::make('manage-performances')
                ->label(__('filament.actions.performances.manage_performances'))
                ->action(fn ($livewire, array $data) => static::saveArtists($livewire->getOwnerRecord(), Arr::get($data, PerformanceForm::REPEATER_PERFORMANCES)))
                ->schema(PerformanceForm::performancesFields()),
        ];
    }

    /**
     * Format artists to the action.
     *
     * @return array<int, array>
     */
    public static function formatArtists(?Song $song = null): array
    {
        if (! ($song instanceof Song)) {
            return [];
        }

        $song->load(Song::RELATION_PERFORMANCE_ARTISTS);

        $performances = $song->performances;

        $artists = [];
        $memberships = [];
        foreach ($performances as $performance) {
            if ($performance->isMembership()) {
                $artists[$performance->artist->artist_id] = [
                    Performance::ATTRIBUTE_ARTIST_TYPE => $performance->artist_type,
                    Performance::ATTRIBUTE_ARTIST_ID => $performance->artist->artist_id,
                    Performance::ATTRIBUTE_ALIAS => $performance->alias,
                    Performance::ATTRIBUTE_AS => $performance->as,
                ];
                $memberships[] = [
                    Membership::ATTRIBUTE_ARTIST => $performance->artist->artist_id,
                    Membership::ATTRIBUTE_MEMBER => $performance->artist->member_id,
                    Membership::ATTRIBUTE_ALIAS => $performance->artist->alias,
                    Membership::ATTRIBUTE_AS => $performance->artist->as,
                ];
                continue;
            }

            $artists[] = [
                Performance::ATTRIBUTE_ARTIST_TYPE => $performance->artist_type,
                Performance::ATTRIBUTE_ARTIST_ID => $performance->artist_id,
                Performance::ATTRIBUTE_ALIAS => $performance->alias,
                Performance::ATTRIBUTE_AS => $performance->as,
            ];
        }

        foreach (array_keys($artists) as $groupId) {
            $membershipsForGroup = Arr::where($memberships, fn ($value): bool => $value[Membership::ATTRIBUTE_ARTIST] === $groupId);

            $artists[$groupId][PerformanceForm::REPEATER_MEMBERSHIPS] = $membershipsForGroup;
        }

        return $artists;
    }

    /**
     * Save the artists to the action.
     */
    public static function saveArtists(Song|int|null $song = null, ?array $data = []): void
    {
        if (is_null($song) || blank($data)) {
            return;
        }

        $action = new ManageSongPerformances();

        $action->forSong($song);

        foreach ($data as $artist) {
            $groupOrArtist = intval(Arr::get($artist, Artist::ATTRIBUTE_ID));

            if (blank(Arr::get($artist, PerformanceForm::REPEATER_MEMBERSHIPS))) {
                $action->addSingleArtist(
                    $groupOrArtist,
                    Arr::get($artist, Performance::ATTRIBUTE_ALIAS),
                    Arr::get($artist, Performance::ATTRIBUTE_AS),
                );
                continue;
            }

            $group = $groupOrArtist;

            $action->addGroupData(
                $group,
                Arr::get($artist, Performance::ATTRIBUTE_ALIAS),
                Arr::get($artist, Performance::ATTRIBUTE_AS),
            );

            foreach (Arr::get($artist, PerformanceForm::REPEATER_MEMBERSHIPS) as $membership) {
                $action->addMembership(
                    $group,
                    intval(Arr::get($membership, Membership::ATTRIBUTE_MEMBER)),
                    Arr::get($membership, Membership::ATTRIBUTE_ALIAS),
                    Arr::get($membership, Membership::ATTRIBUTE_AS),
                );
            }
        }

        $action->commit();
    }
}
