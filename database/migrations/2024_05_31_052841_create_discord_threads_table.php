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
        if (! Schema::hasTable('discord_threads')) {
            Schema::create('discord_threads', function (Blueprint $table) {
                $table->timestamps(6);
                $table->string('thread_id')->primary();
                $table->string('name');

                $table->unsignedBigInteger('anime_id');
                $table->foreign('anime_id')->references('anime_id')->on('anime')->cascadeOnDelete();
            });
        }
    }
};
