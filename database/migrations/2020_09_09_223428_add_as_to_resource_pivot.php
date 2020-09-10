<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAsToResourcePivot extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('anime_resource', function (Blueprint $table) {
            $table->string('as')->nullable();
        });

        Schema::table('artist_resource', function (Blueprint $table) {
            $table->string('as')->nullable();
        });

        Schema::table('resource', function (Blueprint $table) {
            $table->dropColumn('label');
            $table->integer('external_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('anime_resource', function (Blueprint $table) {
            $table->dropColumn('as');
        });

        Schema::table('artist_resource', function (Blueprint $table) {
            $table->dropColumn('as');
        });

        Schema::table('resource', function (Blueprint $table) {
            $table->dropColumn('external_id');
        });
    }
}
