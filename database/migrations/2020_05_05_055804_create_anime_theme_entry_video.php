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
        if (! Schema::hasTable('anime_theme_entry_video')) {
            Schema::create('anime_theme_entry_video', function (Blueprint $table) {
                $table->timestamps(6);
                $table->unsignedBigInteger('entry_id');
                $table->foreign('entry_id')->references('entry_id')->on('anime_theme_entries')->cascadeOnDelete();
                $table->unsignedBigInteger('video_id');
                $table->foreign('video_id')->references('video_id')->on('videos')->cascadeOnDelete();
                $table->primary(['entry_id', 'video_id']);
            });
        }
    }
};
