<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Audio;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Group;
use App\Models\Wiki\Image;
use App\Models\Wiki\Song;
use App\Models\Wiki\Song\Performance;
use App\Models\Wiki\Studio;
use App\Models\Wiki\Synonym;
use App\Models\Wiki\Video;
use App\Models\Wiki\Video\VideoScript;
use Illuminate\Database\Seeder;

class FactoryDataSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Anime::factory()
            ->count(fake()->numberBetween(2, 8))
            ->has(Synonym::factory()->count(fake()->numberBetween(1, 3)), Anime::RELATION_SYNONYMS)
            ->has(ExternalResource::factory()->count(fake()->numberBetween(1, 3)), Anime::RELATION_RESOURCES)
            ->has(Image::factory()->count(fake()->numberBetween(1, 3)), Anime::RELATION_IMAGES)
            ->has(Studio::factory()->has(Image::factory())->count(fake()->numberBetween(1, 3)), Anime::RELATION_STUDIOS)
            ->has(
                AnimeTheme::factory()
                    ->count(fake()->numberBetween(1, 5))
                    ->for(Group::factory(), AnimeTheme::RELATION_GROUP)
                    ->for(
                        Song::factory()->has(
                            Performance::factory()
                                ->count(fake()->numberBetween(1, 4))
                                ->for(
                                    Artist::factory()
                                        ->has(Synonym::factory()->count(fake()->numberBetween(1, 3)), Artist::RELATION_SYNONYMS)
                                        ->has(ExternalResource::factory()->count(fake()->numberBetween(1, 3)), Artist::RELATION_RESOURCES)
                                        ->has(Image::factory()->count(fake()->numberBetween(1, 3)), Artist::RELATION_IMAGES),
                                    Performance::RELATION_ARTIST
                                )
                                ->potentialMember(),
                            Song::RELATION_PERFORMANCES
                        ),
                        AnimeTheme::RELATION_SONG
                    )
                    ->has(
                        AnimeThemeEntry::factory()
                            ->count(fake()->numberBetween(1, 5))
                            ->has(
                                Video::factory()
                                    ->count(fake()->numberBetween(1, 3))
                                    ->for(Audio::factory(), Video::RELATION_AUDIO)
                                    ->has(VideoScript::factory(), Video::RELATION_SCRIPT)
                            )
                    )
            )
            ->has(
                AnimeTheme::factory()
                    ->count(fake()->numberBetween(1, 5))
                    ->for(
                        Song::factory()->has(
                            Performance::factory()
                                ->count(fake()->numberBetween(1, 4))
                                ->for(
                                    Artist::factory()
                                        ->has(Synonym::factory()->count(fake()->numberBetween(1, 3)), Artist::RELATION_SYNONYMS)
                                        ->has(ExternalResource::factory()->count(fake()->numberBetween(1, 3)), Artist::RELATION_RESOURCES)
                                        ->has(Image::factory()->count(fake()->numberBetween(1, 3)), Artist::RELATION_IMAGES),
                                    Performance::RELATION_ARTIST
                                )
                                ->potentialMember(),
                            Song::RELATION_PERFORMANCES
                        ),
                        AnimeTheme::RELATION_SONG
                    )
                    ->has(
                        AnimeThemeEntry::factory()
                            ->count(fake()->numberBetween(1, 5))
                            ->has(
                                Video::factory()
                                    ->count(fake()->numberBetween(1, 3))
                                    ->for(Audio::factory(), Video::RELATION_AUDIO)
                                    ->has(VideoScript::factory(), Video::RELATION_SCRIPT)
                            )
                    )
            )
            ->create();
    }
}
