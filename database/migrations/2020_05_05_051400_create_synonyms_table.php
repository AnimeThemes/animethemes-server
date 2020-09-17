<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSynonymsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('synonym', function (Blueprint $table) {
            $table->id('synonym_id');
            $table->timestamps();
            $table->string('text')->nullable();

            $table->unsignedBigInteger('anime_id');
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
        Schema::dropIfExists('synonym');
    }
}
