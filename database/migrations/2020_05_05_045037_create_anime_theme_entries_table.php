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
        if (! Schema::hasTable('anime_theme_entries')) {
            Schema::create('anime_theme_entries', function (Blueprint $table) {
                $table->id('entry_id');
                $table->timestamps(6);
                $table->softDeletes(precision: 6);
                $table->integer('version');
                $table->string('episodes')->nullable();
                $table->boolean('nsfw')->default(false);
                $table->boolean('spoiler')->default(false);
                $table->text('notes')->nullable();

                $table->unsignedBigInteger('theme_id');
                $table->foreign('theme_id')->references('theme_id')->on('anime_themes')->cascadeOnDelete();
            });
        }
    }
};
