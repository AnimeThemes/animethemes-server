<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCascadeDeletesToTheme extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('theme', function (Blueprint $table) {
            $table->dropForeign('theme_anime_id_foreign');
            $table->foreign('anime_id')->references('anime_id')->on('anime')->onDelete('cascade');

            $table->dropForeign('theme_song_id_foreign');
            $table->foreign('song_id')->references('song_id')->on('song')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('theme', function (Blueprint $table) {
            $table->dropForeign('theme_anime_id_foreign');
            $table->foreign('anime_id')->references('anime_id')->on('anime');

            $table->dropForeign('theme_song_id_foreign');
            $table->foreign('song_id')->references('song_id')->on('song');
        });
    }
}
