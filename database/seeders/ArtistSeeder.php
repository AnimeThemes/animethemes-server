<?php

namespace Database\Seeders;

use App\Enums\ResourceSite;
use App\Models\Artist;
use App\Models\ExternalResource;
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
        $artist_wiki_contents = file_get_contents(WikiPages::ARTIST_INDEX);
        $artist_wiki_json = json_decode($artist_wiki_contents);
        $artist_wiki_content_md = $artist_wiki_json->data->content_md;

        // Match Artist Entries
        // Format: "[{Artist Name}](/r/AnimeThemes/wiki/artist/{Artist Slug}/)"
        preg_match_all('/\[(.*)\]\(\/r\/AnimeThemes\/wiki\/artist\/(.*)\)/m', $artist_wiki_content_md, $artist_wiki_entries, PREG_SET_ORDER);

        foreach ($artist_wiki_entries as $artist_wiki_entry) {
            $artist_name = html_entity_decode($artist_wiki_entry[1]);
            $artist_slug = html_entity_decode($artist_wiki_entry[2]);

            // Create artist if it doesn't already exist
            $artist = Artist::where('name', $artist_name)->where('slug', $artist_slug)->first();
            if ($artist === null) {
                Log::info("Creating artist with name '{$artist_name}' and slug '{$artist_slug}'");
                $artist = Artist::create([
                    'name' => $artist_name,
                    'slug' => $artist_slug,
                ]);
            }

            // Try not to upset Reddit
            sleep(rand(5, 15));

            // Get JSON of Artist Entry page content
            $artist_link = WikiPages::getArtistPage($artist_slug);
            $artist_resource_wiki_contents = file_get_contents($artist_link);
            $artist_resource_wiki_json = json_decode($artist_resource_wiki_contents);
            $artist_resource_wiki_content_md = $artist_resource_wiki_json->data->content_md;

            // Match headers of Resource in Artist Entry page
            // Format: "##[{Artist Name}]({Resource Link})"
            preg_match('/##\[.*\]\((https\:\/\/.*)\)/m', $artist_resource_wiki_content_md, $artist_resource_entry);
            $artist_resource_link = html_entity_decode($artist_resource_entry[1]);
            preg_match('/([0-9]+)/', $artist_resource_link, $external_id);
            $resource_site = ResourceSite::valueOf($artist_resource_link);

            // Create Resource Model with link and derived site
            $resource = ExternalResource::where('site', $resource_site)->where('link', $artist_resource_link)->first();
            if ($resource === null) {
                Log::info("Creating resource with site '{$resource_site}' and link '{$artist_resource_link}'");
                $resource = ExternalResource::create([
                    'site' => $resource_site,
                    'link' => $artist_resource_link,
                    'external_id' => intval($external_id[1]),
                ]);
            }

            // Attach resource to artist if needed
            if (! $artist->externalResources->contains($resource)) {
                Log::info("Attaching resource '{$resource->link}' to artist '{$artist->name}'");
                $resource->artists()->attach($artist);
            }
        }
    }
}
