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
        if (! Schema::hasTable('external_entries')) {
            Schema::create('external_entries', function (Blueprint $table) {
                $table->timestamps(6);
                $table->id('entry_id');
                $table->float('score')->nullable();
                $table->integer('watch_status');
                $table->boolean('is_favorite')->default(false);

                $table->unsignedBigInteger('anime_id')->nullable();
                $table->foreign('anime_id')->references('anime_id')->on('anime')->nullOnDelete();

                $table->unsignedBigInteger('profile_id');
                $table->foreign('profile_id')->references('profile_id')->on('external_profiles')->cascadeOnDelete();
            });
        }
    }
};
