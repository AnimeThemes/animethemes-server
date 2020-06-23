<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCascadeDeletesToEntry extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('entry', function (Blueprint $table) {
            $table->dropForeign('entry_theme_id_foreign');
            $table->foreign('theme_id')->references('theme_id')->on('theme')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('entry', function (Blueprint $table) {
            $table->dropForeign('entry_theme_id_foreign');
            $table->foreign('theme_id')->references('theme_id')->on('theme');
        });
    }
}
