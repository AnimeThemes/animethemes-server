<?php

declare(strict_types=1);

use App\Models\Wiki\Song\Performance;
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
        if (!Schema::hasIndex(Performance::TABLE, 'unique_performance')) {
            Schema::table(Performance::TABLE, function (Blueprint $table) {
                $table->unique([Performance::ATTRIBUTE_SONG, Performance::ATTRIBUTE_ARTIST_TYPE, Performance::ATTRIBUTE_ARTIST_ID], 'unique_performance');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(Performance::TABLE, function (Blueprint $table) {
            $table->dropUnique('unique_performance');
        });
    }
};
