<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('artist_song')) {
            Schema::create('artist_song', function (Blueprint $table) {
                $table->timestamps(6);
                $table->unsignedBigInteger('artist_id');
                $table->foreign('artist_id')->references('artist_id')->on('artists')->cascadeOnDelete();
                $table->unsignedBigInteger('song_id');
                $table->foreign('song_id')->references('song_id')->on('songs')->cascadeOnDelete();
                $table->primary(['artist_id', 'song_id']);
                $table->string('as')->nullable();
                $table->string('alias')->nullable();
            });
        }
    }
};
