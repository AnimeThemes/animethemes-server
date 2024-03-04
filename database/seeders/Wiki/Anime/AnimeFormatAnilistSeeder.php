<?php

declare(strict_types=1);

namespace Database\Seeders\Wiki\Anime;

use App\Enums\Models\Wiki\AnimeMediaFormat;
use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class AnimeFormatAnilistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->anilistSeeder();
    }

    protected function anilistSeeder(): void
    {
        $query = '
            query ($id: Int) {
                Media (id: $id, type: ANIME) {
                    format
                }
            }
        ';

        $chunkSize = 5;
        $animes = Anime::query()->where(Anime::ATTRIBUTE_MEDIA_FORMAT, null)->get();

        foreach ($animes->chunk($chunkSize) as $chunk) {
            foreach ($chunk as $anime) {
                $resource = $anime->resources()->firstWhere(ExternalResource::ATTRIBUTE_SITE, ResourceSite::ANILIST->value);

                if ($resource instanceof ExternalResource) {
                    $variables = [
                        'id' => $resource->external_id
                    ];

                    $response = Http::post('https://graphql.anilist.co', [
                        'query' => $query,
                        'variables' => $variables
                    ]);

                    $format = Arr::get($response, 'data.Media.format');

                    if ($format !== null) {
                        if (in_array($format, ['TV', 'TV_SHORT', 'MOVIE'], true)) {
                            $formats = [
                                'TV' => AnimeMediaFormat::TV->value,
                                'TV_SHORT' => AnimeMediaFormat::TV_SHORT->value,
                                'MOVIE' => AnimeMediaFormat::MOVIE->value
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
                        echo 'format null';
                        echo "\n";
                    }
                }
            }
            sleep(11);
        }
    }
}
