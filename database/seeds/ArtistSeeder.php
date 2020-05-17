<?php

use App\Models\Artist;
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
        DB::table('artist_song')->delete();
        DB::table('artist_resource')->delete();
        DB::table('artist')->delete();

        $artist_wiki_contents = file_get_contents('https://old.reddit.com/r/AnimeThemes/wiki/artist.json');
        $artist_wiki_json = json_decode($artist_wiki_contents);
        $artist_wiki_content_md = $artist_wiki_json->data->content_md;

        preg_match_all('/\[.*\]\(.*artist.*\)/m', $artist_wiki_content_md, $artist_wiki_entries, PREG_PATTERN_ORDER);

        foreach ($artist_wiki_entries[0] as $artist_wiki_entry) {
            preg_match('/\[(.*)\]/', $artist_wiki_entry, $artist_wiki_entry_name);
            preg_match('/\[.*]\(\/r\/AnimeThemes\/wiki\/artist\/(.*)\)/', $artist_wiki_entry, $artist_wiki_entry_alias);

            Artist::create([
                'name' => $artist_wiki_entry_name[1],
                'alias' => $artist_wiki_entry_alias[1]
            ]);
        }
    }
}
