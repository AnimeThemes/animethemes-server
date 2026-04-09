<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Song\RelationManagers;

use App\Actions\Models\Wiki\Song\ManageSongPerformances;
use App\Filament\RelationManagers\Wiki\Song\PerformanceRelationManager;
use App\Filament\Resources\Wiki\Song\Performance\Schemas\PerformanceForm;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Song;
use App\Models\Wiki\Song\Performance;
use Filament\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

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
        )
            ->reorderable(Performance::ATTRIBUTE_RELEVANCE)
            ->defaultSort(Performance::ATTRIBUTE_RELEVANCE);
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

        $song->load(Song::RELATION_PERFORMANCES);

        return $song->performances
            ->sortBy(Performance::ATTRIBUTE_RELEVANCE)
            ->groupBy(Performance::ATTRIBUTE_ARTIST)
            ->mapWithKeys(fn (Collection $performances, $artistId): array => [
                Str::uuid()->__toString() => [
                    Performance::ATTRIBUTE_ARTIST => $artistId,
                    Performance::ATTRIBUTE_AS => $performances->first()->getAttribute(Performance::ATTRIBUTE_AS),
                    Performance::ATTRIBUTE_ALIAS => $performances->first()->getAttribute(Performance::ATTRIBUTE_ALIAS),
                    PerformanceForm::REPEATER_MEMBERS => $performances
                        ->filter(fn (Performance $performance): bool => filled($performance->getAttribute(Performance::ATTRIBUTE_MEMBER)))
                        ->mapWithKeys(fn (Performance $performance): array => [
                            Str::uuid()->__toString() => [
                                Performance::ATTRIBUTE_MEMBER => $performance->getAttribute(Performance::ATTRIBUTE_MEMBER),
                                Performance::ATTRIBUTE_MEMBER_ALIAS => $performance->getAttribute(Performance::ATTRIBUTE_MEMBER_ALIAS),
                                Performance::ATTRIBUTE_MEMBER_AS => $performance->getAttribute(Performance::ATTRIBUTE_MEMBER_AS),
                            ],
                        ])->all(),
                ],
            ])
            ->all();
    }

    /**
     * Save the artists to the action.
     */
    public static function saveArtists(Song|int|null $song = null, ?array $performances = []): void
    {
        if (is_null($song) || blank($performances)) {
            return;
        }

        $action = new ManageSongPerformances($song);

        foreach ($performances as $performance) {
            $artist = intval(Arr::get($performance, Artist::ATTRIBUTE_ID));
            $artistAlias = Arr::get($performance, Performance::ATTRIBUTE_ALIAS);
            $artistAs = Arr::get($performance, Performance::ATTRIBUTE_AS);

            if (blank(Arr::get($performance, PerformanceForm::REPEATER_MEMBERS))) {
                $action->addArtist(
                    $artist,
                    alias: $artistAlias,
                    as: $artistAs,
                );
                continue;
            }

            foreach (Arr::get($performance, PerformanceForm::REPEATER_MEMBERS) as $member) {
                $action->addArtist(
                    $artist,
                    intval(Arr::get($member, Performance::ATTRIBUTE_MEMBER)),
                    $artistAlias,
                    $artistAs,
                    Arr::get($member, Performance::ATTRIBUTE_MEMBER_ALIAS),
                    Arr::get($member, Performance::ATTRIBUTE_MEMBER_AS),
                );
            }
        }

        $action->commit();
    }
}
