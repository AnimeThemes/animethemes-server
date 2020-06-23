<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCascadeDeletesToSynonym extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('synonym', function (Blueprint $table) {
            $table->dropForeign('synonym_anime_id_foreign');
            $table->foreign('anime_id')->references('anime_id')->on('anime')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('synonym', function (Blueprint $table) {
            $table->dropForeign('synonym_anime_id_foreign');
            $table->foreign('anime_id')->references('anime_id')->on('anime');
        });
    }
}
