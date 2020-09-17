<?php

use App\Enums\OverlapType;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('video', function (Blueprint $table) {
            $table->id('video_id');
            $table->timestamps();
            $table->string('basename');
            $table->string('filename');
            $table->string('path');
            $table->integer('resolution')->nullable();
            $table->boolean('nc')->default(false);
            $table->boolean('subbed')->default(false);
            $table->boolean('lyrics')->default(false);
            $table->boolean('uncen')->default(false);
            $table->integer('overlap')->default(OverlapType::NONE);
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
        Schema::dropIfExists('video');
    }
}
