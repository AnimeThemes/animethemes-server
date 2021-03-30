<?php

namespace Database\Seeders;

use App\Enums\ResourceSite;
use App\Models\Anime;
use App\Models\ExternalResource;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class AnimeResourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (WikiPages::YEAR_MAP as $year_page => $years) {

            // Try not to upset Reddit
            sleep(rand(5, 15));

            // Get JSON of Year page content
            $year_wiki_contents = WikiPages::getPageContents($year_page);
            if ($year_wiki_contents === null) {
                continue;
            }

            // Match headers of Anime and links of Resources
            // Format: "###[{Anime Name}]({Resource Link})"
            preg_match_all('/###\[(.*)\]\((https\:\/\/.*)\)/m', $year_wiki_contents, $anime_resource_wiki_entries, PREG_SET_ORDER);

            foreach ($anime_resource_wiki_entries as $anime_resource_wiki_entry) {
                $anime_name = html_entity_decode($anime_resource_wiki_entry[1]);
                $resource_link = html_entity_decode($anime_resource_wiki_entry[2]);
                preg_match('/([0-9]+)/', $resource_link, $external_id);

                // Create Resource Model with link and derived site if it doesn't already exist
                $resource = ExternalResource::where('site', ResourceSite::valueOf($resource_link))->where('link', $resource_link)->first();
                if ($resource === null) {
                    Log::info("Creating resource '{$resource_link}'");
                    $resource = ExternalResource::create(
                        [
                            'site' => ResourceSite::valueOf($resource_link),
                            'link' => $resource_link,
                            'external_id' => intval($external_id[1]),
                        ]
                    );
                }

                try {
                    // Attach Anime to Resource by Name if we have a definitive match
                    // This is not guaranteed as an Anime Name may be inconsistent between indices
                    $resource_anime = Anime::where('name', $anime_name)
                        ->whereIn('year', $years)
                        ->whereDoesntHave('externalResources', function ($resource_query) use ($resource) {
                            $resource_query->where('site', $resource->site->value)->where('link', $resource->link);
                        })
                        ->get();
                    if ($resource_anime->count() === 1) {
                        Log::info("Attaching resource '{$resource_link}' to anime '{$anime_name}'");
                        $resource->anime()->attach($resource_anime);
                    }
                } catch (\Exception $exception) {
                    Log::error($exception->getMessage());
                }
            }
        }
    }
}
