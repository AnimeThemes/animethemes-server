<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeSynonym;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Artist;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Image;
use App\Models\Wiki\Series;
use App\Models\Wiki\Song;
use App\Models\Wiki\Studio;
use App\Models\Wiki\Video;
use App\Pivots\AnimeImage;
use App\Pivots\AnimeResource;
use App\Pivots\AnimeSeries;
use App\Pivots\AnimeStudio;
use App\Pivots\AnimeThemeEntryVideo;
use App\Pivots\ArtistImage;
use App\Pivots\ArtistMember;
use App\Pivots\ArtistResource;
use App\Pivots\ArtistSong;
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

        DB::table(AnimeThemeEntryVideo::TABLE)->truncate();
        DB::table(AnimeThemeEntry::TABLE)->truncate();
        DB::table(AnimeTheme::TABLE)->truncate();
        DB::table(AnimeSynonym::TABLE)->truncate();
        DB::table(AnimeResource::TABLE)->truncate();
        DB::table(AnimeImage::TABLE)->truncate();
        DB::table(AnimeSeries::TABLE)->truncate();
        DB::table(AnimeStudio::TABLE)->truncate();
        DB::table(Anime::TABLE)->truncate();

        DB::table(ArtistResource::TABLE)->truncate();
        DB::table(ArtistImage::TABLE)->truncate();
        DB::table(ArtistSong::TABLE)->truncate();
        DB::table(ArtistMember::TABLE)->truncate();
        DB::table(Artist::TABLE)->truncate();

        DB::table(ExternalResource::TABLE)->truncate();
        DB::table(Image::TABLE)->truncate();
        DB::table(Series::TABLE)->truncate();
        DB::table(Studio::TABLE)->truncate();
        DB::table(Song::TABLE)->truncate();
        DB::table(Video::TABLE)->truncate();

        DB::table('audits')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
