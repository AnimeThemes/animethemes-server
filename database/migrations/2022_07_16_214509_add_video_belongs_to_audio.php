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
        if (! Schema::hasColumn('videos', 'audio_id')) {
            Schema::table('videos', function (Blueprint $table) {
                $table->unsignedBigInteger('audio_id')->nullable();
                $table->foreign('audio_id')->references('audio_id')->on('audios')->nullOnDelete();
            });
        }
    }
};
