<?php

use App\Enums\ResourceType;
use App\Models\Artist;
use App\Models\ExternalResource;
use Illuminate\Database\Seeder;

class ArtistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Remove any existing rows in Artist-related tables
        // We want these tables to match the subreddit wiki
        DB::table('artist_song')->delete();
        DB::table('artist_resource')->delete();
        DB::table('artist')->delete();

        // Get JSON of Artist Index page content
        $artist_wiki_contents = file_get_contents('https://old.reddit.com/r/AnimeThemes/wiki/artist.json');
        $artist_wiki_json = json_decode($artist_wiki_contents);
        $artist_wiki_content_md = $artist_wiki_json->data->content_md;

        // Match Artist Entries
        // Format: "[{Artist Name}](/r/AnimeThemes/wiki/artist/{Artist Alias}/)
        preg_match_all('/\[(.*)\]\((\/r\/AnimeThemes\/wiki\/artist\/(.*))\)/m', $artist_wiki_content_md, $artist_wiki_entries, PREG_SET_ORDER);

        foreach ($artist_wiki_entries as $artist_wiki_entry) {
            $artist_name = html_entity_decode($artist_wiki_entry[1]);
            $artist_link = 'https://old.reddit.com' . $artist_wiki_entry[2] . '.json';
            $artist_alias = html_entity_decode($artist_wiki_entry[3]);

            // Create Model from subreddit Alias & Name
            $artist = Artist::create([
                'name' => $artist_name,
                'alias' => $artist_alias
            ]);

            // Try not to upset Reddit
            sleep(rand(5, 15));

            // Retrieve JSON of Artist Entry page content
            $artist_resource_wiki_contents = file_get_contents($artist_link);
            $artist_resource_wiki_json = json_decode($artist_resource_wiki_contents);
            $artist_resource_wiki_content_md = $artist_resource_wiki_json->data->content_md;

            // Match headers of Resource in Artist Entry page
            preg_match('/##\[.*\]\((https\:\/\/.*)\)/m', $artist_resource_wiki_content_md, $artist_resource_entry);
            $artist_resource_link = html_entity_decode($artist_resource_entry[1]);

            // Create Resource Model with link and derived type
            $resource = ExternalResource::create([
                'type' => ResourceType::valueOf($artist_resource_link),
                'link' => $artist_resource_link
            ]);

            // Attach Artist to Resource
            $resource->artists()->sync($artist);
        }
    }
}
