<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateEntryVideo
 */
class CreateEntryVideo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entry_video', function (Blueprint $table) {
            $table->timestamps(6);
            $table->unsignedBigInteger('entry_id');
            $table->foreign('entry_id')->references('entry_id')->on('entry')->onDelete('cascade');
            $table->unsignedBigInteger('video_id');
            $table->foreign('video_id')->references('video_id')->on('video')->onDelete('cascade');
            $table->primary(['entry_id', 'video_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('entry_video');
    }
}
