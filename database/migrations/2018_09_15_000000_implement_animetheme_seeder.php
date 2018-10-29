<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ImplementAnimeThemeSeeder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('animes', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name'); // main name
            $table->char('collection', 4); // collection eg.: 2018, 90s, misc
            $table->char('season', 1); // season eg.: 0-Winter, 1-Spring, 2-Summer, 3-Fall, 4-All
            $table->integer('mal_id')->nullable($value = true)->unique();
            $table->integer('anilist_id')->nullable($value = true)->unique();
            $table->integer('kitsu_id')->nullable($value = true)->unique();
            $table->integer('anidb_id')->nullable($value = true)->unique();

            $table->timestamps();
        });

        Schema::create('anime_names', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('anime_id')->unsigned();
            $table->string('title');
            $table->char('language', 5);
            $table->timestamps();
            $table->foreign('anime_id')->references('id')->on('animes');
        });

        Schema::create('themes', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('anime_id')->unsigned();
            $table->string('song_name');
            $table->boolean('isNSFW');
            $table->boolean('isSpoiler');
            $table->char('theme', 2);
            $table->integer('ver_major'); // OP1
            $table->integer('ver_minor'); // OP1 V1
            $table->string('episodes')->nullable($value = true);
            $table->text('notes')->nullable($value = true);
            $table->timestamps();
            $table->foreign('anime_id')->references('id')->on('animes');
        });

        Schema::table('videos', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('theme_id')->nullable($value = true)->unsigned();
            $table->integer('quality')->nullable($value = true);
            $table->boolean('isNC')->nullable($value = true);
            $table->boolean('isLyrics')->nullable($value = true);
            $table->boolean('isSubbed')->nullable($value = true);
            $table->boolean('isUncensored')->nullable($value = true);
            $table->boolean('isTrans')->nullable($value = true);
            $table->boolean('isOver')->nullable($value = true);
            $table->string('source')->nullable($value = true);
            $table->foreign('theme_id')->references('id')->on('themes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('animes');
        Schema::dropIfExists('anime_names');
        Schema::dropIfExists('themes');
        Schema::table('videos', function (Blueprint $table) {
            $table->dropForeign(['theme_id']);
            $table->dropColumn('theme_id');
            $table->dropColumn('quality');
            $table->dropColumn('isNC');
            $table->dropColumn('isLyrics');
            $table->dropColumn('source');
        });
    }
}
