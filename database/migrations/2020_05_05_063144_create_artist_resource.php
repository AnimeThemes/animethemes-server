<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArtistResource extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('artist_resource', function (Blueprint $table) {
            $table->unsignedBigInteger('artist_id');
            $table->foreign('artist_id')->references('artist_id')->on('artist')->onDelete('cascade');
            $table->unsignedBigInteger('resource_id');
            $table->foreign('resource_id')->references('resource_id')->on('resource')->onDelete('cascade');
            $table->primary(['artist_id', 'resource_id']);
            $table->string('as')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('artist_resource');
    }
}
