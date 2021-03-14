<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entry', function (Blueprint $table) {
            $table->id('entry_id');
            $table->timestamps(6);
            $table->softDeletes('deleted_at', 6);
            $table->integer('version')->nullable();
            $table->string('episodes')->nullable();
            $table->boolean('nsfw')->default(false);
            $table->boolean('spoiler')->default(false);
            $table->text('notes')->nullable();

            $table->unsignedBigInteger('theme_id');
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
        Schema::dropIfExists('entry');
    }
}
