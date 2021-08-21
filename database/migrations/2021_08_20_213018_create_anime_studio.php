<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateAnimeStudio.
 */
class CreateAnimeStudio extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('anime_studio', function (Blueprint $table) {
            $table->timestamps(6);
            $table->unsignedBigInteger('anime_id');
            $table->foreign('anime_id')->references('anime_id')->on('anime')->onDelete('cascade');
            $table->unsignedBigInteger('studio_id');
            $table->foreign('studio_id')->references('studio_id')->on('studios')->onDelete('cascade');
            $table->primary(['anime_id', 'studio_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('anime_studio');
    }
}
