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
        if (! Schema::hasTable('watch_history')) {
            Schema::create('watch_history', function (Blueprint $table) {
                $table->id('id');

                $table->unsignedBigInteger('entry_id');
                $table->foreign('entry_id')->references('entry_id')->on('anime_theme_entries')->cascadeOnDelete();

                $table->unsignedBigInteger('user_id');
                $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();

                $table->unsignedBigInteger('video_id');
                $table->foreign('video_id')->references('video_id')->on('videos')->cascadeOnDelete();

                $table->timestamps(6);
            });
        }
    }
};
