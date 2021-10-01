<?php

declare(strict_types=1);

use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use App\Pivots\AnimeThemeEntryVideo;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateAnimeThemeEntryVideo.
 */
class CreateAnimeThemeEntryVideo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(AnimeThemeEntryVideo::TABLE, function (Blueprint $table) {
            $table->timestamps(6);
            $table->unsignedBigInteger(AnimeThemeEntryVideo::ATTRIBUTE_ENTRY);
            $table->foreign(AnimeThemeEntryVideo::ATTRIBUTE_ENTRY)->references(AnimeThemeEntry::ATTRIBUTE_ID)->on(AnimeThemeEntry::TABLE)->cascadeOnDelete();
            $table->unsignedBigInteger(AnimeThemeEntryVideo::ATTRIBUTE_VIDEO);
            $table->foreign(AnimeThemeEntryVideo::ATTRIBUTE_VIDEO)->references(Video::ATTRIBUTE_ID)->on('videos')->cascadeOnDelete();
            $table->primary([AnimeThemeEntryVideo::ATTRIBUTE_ENTRY, AnimeThemeEntryVideo::ATTRIBUTE_VIDEO]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(AnimeThemeEntryVideo::TABLE);
    }
}
