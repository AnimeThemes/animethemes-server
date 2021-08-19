<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Class WikiDeseeder.
 */
class WikiDeseeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        DB::table('anime_theme_entry_video')->truncate();
        DB::table('anime_theme_entries')->truncate();
        DB::table('anime_themes')->truncate();
        DB::table('anime_synonyms')->truncate();
        DB::table('anime_resource')->truncate();
        DB::table('anime_series')->truncate();
        DB::table('anime')->truncate();

        DB::table('artist_resource')->truncate();
        DB::table('artist_song')->truncate();
        DB::table('artists')->truncate();

        DB::table('resources')->truncate();
        DB::table('series')->truncate();
        DB::table('songs')->truncate();
        DB::table('videos')->truncate();

        DB::table('audits')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
