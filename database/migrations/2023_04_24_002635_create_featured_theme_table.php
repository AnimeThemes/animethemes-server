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
        if (! Schema::hasTable('featured_themes')) {
            Schema::create('featured_themes', function (Blueprint $table) {
                $table->id('featured_theme_id');
                $table->timestamps(6);
                $table->timestamp('start_at', 6);
                $table->timestamp('end_at', 6);

                $table->unsignedBigInteger('user_id')->nullable();
                $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();

                $table->unsignedBigInteger('entry_id')->nullable();
                $table->foreign('entry_id')->references('entry_id')->on('anime_theme_entries')->nullOnDelete();

                $table->unsignedBigInteger('video_id')->nullable();
                $table->foreign('video_id')->references('video_id')->on('videos')->nullOnDelete();
            });
        }
    }
};
