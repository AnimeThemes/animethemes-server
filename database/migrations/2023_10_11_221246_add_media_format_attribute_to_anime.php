<?php

use App\Models\Wiki\Anime;
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
        Schema::table(Anime::TABLE, function (Blueprint $table) {
            $table->integer(Anime::ATTRIBUTE_MEDIA_FORMAT)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(Anime::TABLE, function (Blueprint $table) {
            $table->dropColumn(Anime::ATTRIBUTE_MEDIA_FORMAT);
        });
    }
};
