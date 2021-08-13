<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateAnimeThemeEntriesTable.
 */
class CreateAnimeThemeEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('anime_theme_entries', function (Blueprint $table) {
            $table->id('anime_theme_entry_id');
            $table->timestamps(6);
            $table->softDeletes('deleted_at', 6);
            $table->integer('version')->nullable();
            $table->string('episodes')->nullable();
            $table->boolean('nsfw')->default(false);
            $table->boolean('spoiler')->default(false);
            $table->text('notes')->nullable();

            $table->unsignedBigInteger('anime_theme_id');
            $table->foreign('anime_theme_id')->references('anime_theme_id')->on('anime_theme_id')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('anime_theme_entries');
    }
}
