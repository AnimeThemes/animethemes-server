<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\Artist;
use App\Models\Wiki\ExternalResource;
use App\Pivots\ArtistResource;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

/**
 * Class ArtistSeeder.
 */
class ArtistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get JSON of Artist Index page content
        $artistWikiContents = WikiPages::getPageContents(WikiPages::ARTIST_INDEX);
        if ($artistWikiContents === null) {
            return;
        }

        // Match Artist Entries
        // Format: "[{Artist Name}](/r/AnimeThemes/wiki/artist/{Artist Slug}/)"
        preg_match_all('/\[(.*)]\(\/r\/AnimeThemes\/wiki\/artist\/(.*)\)/m', $artistWikiContents, $artistWikiEntries, PREG_SET_ORDER);

        foreach ($artistWikiEntries as $artistWikiEntry) {
            $artistName = html_entity_decode($artistWikiEntry[1]);
            $artistSlug = html_entity_decode($artistWikiEntry[2]);

            // Create artist if it doesn't already exist
            $artist = Artist::query()->where('name', $artistName)->where('slug', $artistSlug)->first();
            if ($artist === null) {
                Log::info("Creating artist with name '{$artistName}' and slug '{$artistSlug}'");

                $artist = Artist::factory()->createOne([
                    'name' => $artistName,
                    'slug' => $artistSlug,
                ]);
            }

            // Try not to upset Reddit
            sleep(rand(5, 15));

            // Get JSON of Artist Entry page content
            $artistLink = WikiPages::getArtistPage($artistSlug);
            $artistResourceWikiContents = WikiPages::getPageContents($artistLink);
            if ($artistResourceWikiContents === null) {
                continue;
            }

            // Match headers of Resource in Artist Entry page
            // Format: "##[{Artist Name}]({Resource Link})"
            preg_match('/##\[.*]\((https:\/\/.*)\)/m', $artistResourceWikiContents, $artistResourceEntry);
            $artistResourceLink = html_entity_decode($artistResourceEntry[1]);
            preg_match('/([0-9]+)/', $artistResourceLink, $externalId);
            $resourceSite = ResourceSite::valueOf($artistResourceLink);

            // Create Resource Model with link and derived site
            $resource = ExternalResource::query()
                ->where('site', $resourceSite)
                ->where('link', $artistResourceLink)
                ->first();

            if ($resource === null) {
                Log::info("Creating resource with site '{$resourceSite}' and link '{$artistResourceLink}'");

                $resource = ExternalResource::factory()->createOne([
                    'site' => $resourceSite,
                    'link' => $artistResourceLink,
                    'external_id' => intval($externalId[1]),
                ]);
            }

            // Attach resource to artist if needed
            if (ArtistResource::query()
                ->where($artist->getKeyName(), $artist->getKey())
                ->where($resource->getKeyName(), $resource->getKey())
                ->doesntExist()
            ) {
                Log::info("Attaching resource '{$resource->link}' to artist '{$artist->name}'");
                $resource->artists()->attach($artist);
            }
        }
    }
}
