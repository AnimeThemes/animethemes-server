<?php

namespace Database\Seeders;

use App\Enums\ResourceSite;
use App\Models\Artist;
use App\Models\ExternalResource;
use App\Pivots\ArtistResource;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

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
        preg_match_all('/\[(.*)\]\(\/r\/AnimeThemes\/wiki\/artist\/(.*)\)/m', $artistWikiContents, $artistWikiEntries, PREG_SET_ORDER);

        foreach ($artistWikiEntries as $artistWikiEntry) {
            $artistName = html_entity_decode($artistWikiEntry[1]);
            $artistSlug = html_entity_decode($artistWikiEntry[2]);

            // Create artist if it doesn't already exist
            $artist = Artist::where('name', $artistName)->where('slug', $artistSlug)->first();
            if ($artist === null) {
                Log::info("Creating artist with name '{$artistName}' and slug '{$artistSlug}'");
                $artist = Artist::create([
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
            preg_match('/##\[.*\]\((https\:\/\/.*)\)/m', $artistResourceWikiContents, $artistResourceEntry);
            $artistResourceLink = html_entity_decode($artistResourceEntry[1]);
            preg_match('/([0-9]+)/', $artistResourceLink, $externalId);
            $resourceSite = ResourceSite::valueOf($artistResourceLink);

            // Create Resource Model with link and derived site
            $resource = ExternalResource::where('site', $resourceSite)->where('link', $artistResourceLink)->first();
            if ($resource === null) {
                Log::info("Creating resource with site '{$resourceSite}' and link '{$artistResourceLink}'");
                $resource = ExternalResource::create([
                    'site' => $resourceSite,
                    'link' => $artistResourceLink,
                    'external_id' => intval($externalId[1]),
                ]);
            }

            // Attach resource to artist if needed
            if (ArtistResource::where($artist->getKeyName(), $artist->getKey())->where($resource->getKeyName(), $resource->getKey())->doesntExist()) {
                Log::info("Attaching resource '{$resource->link}' to artist '{$artist->name}'");
                $resource->artists()->attach($artist);
            }
        }
    }
}
