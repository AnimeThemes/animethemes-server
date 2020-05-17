<?php

use App\Models\Anime;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AnimeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('entry_video')->delete();
        DB::table('entry')->delete();
        DB::table('theme')->delete();
        DB::table('synonym')->delete();
        DB::table('anime_resource')->delete();
        DB::table('anime')->delete();

        $anime_wiki_contents = file_get_contents('https://old.reddit.com/r/AnimeThemes/wiki/anime_index.json');
        $anime_wiki_json = json_decode($anime_wiki_contents);
        $anime_wiki_content_md = $anime_wiki_json->data->content_md;

        preg_match_all('/\[.*]\(\/r\/AnimeThemes\/wiki\/(.*)\)/m', $anime_wiki_content_md, $anime_wiki_entries, PREG_PATTERN_ORDER);

        $slugs = [];

        foreach ($anime_wiki_entries[0] as $anime_wiki_entry) {
            preg_match('/\[(.*)\s\(.*\)\]/', $anime_wiki_entry, $anime_wiki_entry_name);
            preg_match('/\[.*\s\((.*)\)\]/', $anime_wiki_entry, $anime_wiki_entry_year);

            $slug = Str::slug($anime_wiki_entry_name[1], '_');
            if (in_array($slug, $slugs)) {
                $slug = Str::slug($anime_wiki_entry_name[1] . ' ' . $anime_wiki_entry_year[1], '_');
            }
            $slugs[] = $slug;

            $year = $anime_wiki_entry_year[1];
            if (strpos($anime_wiki_entry_year[1], 's') !== false) {
                $year = '19' . str_replace('s','', $anime_wiki_entry_year[1]);
            }

            Anime::create([
                'name' => $anime_wiki_entry_name[1],
                'alias' => $slug,
                'year' => $year
            ]);
        }
    }
}
