<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Wiki\Anime;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Synonym;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;

class SynonymsToTitleSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Anime::withoutTimestamps(function (): void {
            Synonym::withoutTimestamps(function (): void {
                Anime::query()
                    ->where(
                        fn (Builder $query) => $query->orWhereNull('title_english')
                            ->orWhereNull('title_native')
                    )
                    ->with('synonyms')
                    ->chunkById(100, function (Collection $animeCollection): void {
                        /** @var Anime $anime */
                        foreach ($animeCollection as $anime) {
                            echo "Updating {$anime->getName()}\n";
                            $anime->update([
                                'title_english' => $anime->synonyms->firstWhere('type', 2)?->text,
                                'title_native' => $anime->synonyms->firstWhere('type', 1)?->text,
                            ]);

                            $anime->synonyms->where('type', 3)->each(fn (Synonym $synonym) => $synonym->update(['language' => 'Short Romaji']));
                        }
                    });
            });
        });

        Artist::withoutTimestamps(function (): void {
            Artist::query()
                ->whereNull('name_native')
                ->with('synonyms')
                ->chunkById(100, function (Collection $artists): void {
                    /** @var Artist $artist */
                    foreach ($artists as $artist) {
                        echo "Updating {$artist->getName()}\n";
                        $artist->update([
                            'name_native' => $artist->synonyms->firstWhere('type', 1)?->text,
                        ]);
                    }
                });
        });
    }
}
