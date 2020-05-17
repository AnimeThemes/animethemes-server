<?php

use App\Models\Series;
use Illuminate\Database\Seeder;

class SeriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('anime_series')->delete();
        DB::table('series')->delete();

        $series_wiki_contents = file_get_contents('https://old.reddit.com/r/AnimeThemes/wiki/series.json');
        $series_wiki_json = json_decode($series_wiki_contents);
        $series_wiki_content_md = $series_wiki_json->data->content_md;

        preg_match_all('/\[.*\]\(.*series.*\)/m', $series_wiki_content_md, $series_wiki_entries, PREG_PATTERN_ORDER);

        foreach ($series_wiki_entries[0] as $series_wiki_entry) {
            preg_match('/\[(.*)\]/', $series_wiki_entry, $series_wiki_entry_name);
            preg_match('/\[.*]\(\/r\/AnimeThemes\/wiki\/series\/(.*)\)/', $series_wiki_entry, $series_wiki_entry_alias);
            //TODO: match link

            Series::create([
                'name' => $series_wiki_entry_name[1],
                'alias' => $series_wiki_entry_alias[1]
            ]);

            //TODO: visit matched link, match anime headers, link anime by name
        }
    }
}
