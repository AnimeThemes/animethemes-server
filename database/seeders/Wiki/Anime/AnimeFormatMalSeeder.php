<?php

declare(strict_types=1);

namespace Database\Seeders\Wiki\Anime;

use App\Enums\Models\Wiki\AnimeMediaFormat;
use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class AnimeFormatMalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->malSeeder();
    }

    protected function malSeeder(): void
    {
        $chunkSize = 10;
        $animes = Anime::query()->where(Anime::ATTRIBUTE_MEDIA_FORMAT, null)->get();

        foreach ($animes->chunk($chunkSize) as $chunk) {
            foreach ($chunk as $anime) {
                $resource = $anime->resources()->firstWhere(ExternalResource::ATTRIBUTE_SITE, ResourceSite::MAL->value);

                if ($resource instanceof ExternalResource) {
                    $response = Http::withHeaders(['X-MAL-CLIENT-ID' => Config::get('services.mal.client')])
                        ->get("https://api.myanimelist.net/v2/anime/$resource->external_id", [
                            'fields' => 'media_type'
                        ])->json();

                    $format = Arr::get($response, 'media_type');
                    
                    if ($format !== null) {
                        if (in_array($format, ['movie', 'tv', 'ova', 'ona', 'special'], true)) {
                            $formats = [
                                'movie' => AnimeMediaFormat::MOVIE->value,
                                'tv' => AnimeMediaFormat::TV->value,
                                'ova' => AnimeMediaFormat::OVA->value,
                                'ona' => AnimeMediaFormat::ONA->value,
                                'special' => AnimeMediaFormat::SPECIAL->value
                            ];

                            $anime->update([
                                Anime::ATTRIBUTE_MEDIA_FORMAT => $formats[$format]
                            ]);
                            echo $format;
                            echo ' -> ';
                            echo $anime->name;
                            echo "\n";
                        } else {
                            echo 'no format include -> ';
                            echo $anime->name;
                            echo "\n";
                        }
                    } else {
                        echo 'format null -> ';
                        echo $anime->name;
                        echo "\n";
                    }
                }
            }
            sleep(5);
        }
    }
}
