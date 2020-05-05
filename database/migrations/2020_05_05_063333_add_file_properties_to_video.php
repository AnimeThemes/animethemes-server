<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFilePropertiesToVideo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('video', function (Blueprint $table) {
            $table->integer('resolution')->nullable();
            $table->boolean('nc')->default(false);
            $table->boolean('subbed')->default(false);
            $table->boolean('lyrics')->default(false);
            $table->boolean('uncen')->default(false);
            $table->boolean('trans')->default(false);
            $table->boolean('over')->default(false);
            $table->integer('source')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('video', function (Blueprint $table) {
            $table->dropColumn('resolution');
            $table->dropColumn('nc');
            $table->dropColumn('subbed');
            $table->dropColumn('lyrics');
            $table->dropColumn('uncen');
            $table->dropColumn('trans');
            $table->dropColumn('over');
            $table->dropColumn('source');
        });
    }
}
