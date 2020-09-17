<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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

        DB::table('entry_video')->truncate();
        DB::table('entry')->truncate();
        DB::table('theme')->truncate();
        DB::table('synonym')->truncate();
        DB::table('anime_resource')->truncate();
        DB::table('anime_series')->truncate();
        DB::table('anime')->truncate();

        DB::table('artist_resource')->truncate();
        DB::table('artist_song')->truncate();
        DB::table('artist')->truncate();

        DB::table('resource')->truncate();
        DB::table('series')->truncate();
        DB::table('song')->truncate();
        DB::table('video')->truncate();

        DB::table('audits')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
