<?php

declare(strict_types=1);

namespace Database\Seeders\Wiki\AnimeSynonym;

use App\Enums\Models\Wiki\AnimeSynonymType;
use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeSynonym;
use App\Models\Wiki\ExternalResource;
use Illuminate\Database\Seeder;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

/**
 * Class AnimeSynonymTypeSeeder.
 */
class AnimeSynonymTypeSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        $this->createNewSynonymsByAnilist();
    }

    protected function createNewSynonymsByAnilist()
    {
        $chunkSize = 5;

        $animes = Anime::query()
            ->where(Anime::ATTRIBUTE_ID, '>', 0)
            ->get();

        foreach ($animes->chunk($chunkSize) as $chunk) {
            foreach ($chunk as $anime) {
                if ($anime instanceof Anime) {
                    $synonyms = $anime->animesynonyms()->get();

                    if (
                        $synonyms->contains(AnimeSynonym::ATTRIBUTE_TYPE, AnimeSynonymType::ENGLISH->value) &&
                        $synonyms->contains(AnimeSynonym::ATTRIBUTE_TYPE, AnimeSynonymType::NATIVE->value)
                    ) continue;

                    $titles = $this->getTitlesAvailable($anime);

                    if ($titles === null) continue;

                    $english = Arr::get($titles, 'english');
                    $native = Arr::get($titles, 'native');

                    if (!$synonyms->contains(AnimeSynonym::ATTRIBUTE_TYPE, AnimeSynonymType::ENGLISH->value)) {
                        if ($anime->name === $english) continue;

                        $newSynonymEnglish = new AnimeSynonym([
                            AnimeSynonym::ATTRIBUTE_TEXT => $english,
                            AnimeSynonym::ATTRIBUTE_TYPE => AnimeSynonymType::ENGLISH->value,
                            AnimeSynonym::ATTRIBUTE_ANIME => $anime->anime_id,
                        ]);

                        $newSynonymEnglish->save();
                        echo "{$newSynonymEnglish->text} -> create english"."\n";
                    }

                    if (!$synonyms->contains(AnimeSynonym::ATTRIBUTE_TYPE, AnimeSynonymType::NATIVE->value)) {
                        if ($anime->name === $native) continue;
                        
                        $newSynonymNative = new AnimeSynonym([
                            AnimeSynonym::ATTRIBUTE_TEXT => $native,
                            AnimeSynonym::ATTRIBUTE_TYPE => AnimeSynonymType::NATIVE->value,
                            AnimeSynonym::ATTRIBUTE_ANIME => $anime->anime_id,
                        ]);

                        $newSynonymNative->save();
                        echo "{$newSynonymNative->text} -> create native"."\n";
                    }
                }
            }
            sleep(11);
        }
    }

    protected function getTitlesAvailable(Anime $anime)
    {
        $anilistResource = $anime->resources()->firstWhere(ExternalResource::ATTRIBUTE_SITE, ResourceSite::ANILIST->value);

        if ($anilistResource === null) return null;

        $query = '
            query ($id: Int) {
                Media(id: $id, type: ANIME) {
                    title {
                        english
                        native
                    }
                }
            }
        ';

        $variables = [
            'id' => $anilistResource->external_id,
        ];

        try {
            echo "{$anime->anime_id} Request"."\n";

            $response = Http::post('https://graphql.anilist.co', [
                'query' => $query,
                'variables' => $variables,
            ])
                ->throw()
                ->json();

        } catch (RequestException $e) {
            echo $e->getMessage();
            return null;
            throw $e;
        }

        return Arr::get($response, 'data.Media.title');
    }
}
