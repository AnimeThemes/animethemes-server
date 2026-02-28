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
        if (! Schema::hasColumn('playlist_tracks', 'position')) {
            Schema::table('playlist_tracks', function (Blueprint $table) {
                $table->integer('position')->default(1);

                $table->index('position');
                $table->index(['playlist_id', 'position']);
            });
        }
    }
};
