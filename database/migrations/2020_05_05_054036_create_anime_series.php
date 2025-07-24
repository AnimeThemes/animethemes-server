<?php

declare(strict_types=1);

use App\Models\Wiki\Anime;
use App\Models\Wiki\Series;
use App\Pivots\Wiki\AnimeSeries;
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
        if (! Schema::hasTable(AnimeSeries::TABLE)) {
            Schema::create(AnimeSeries::TABLE, function (Blueprint $table) {
                $table->timestamps(6);
                $table->unsignedBigInteger(AnimeSeries::ATTRIBUTE_ANIME);
                $table->foreign(AnimeSeries::ATTRIBUTE_ANIME)->references(Anime::ATTRIBUTE_ID)->on(Anime::TABLE)->cascadeOnDelete();
                $table->unsignedBigInteger(AnimeSeries::ATTRIBUTE_SERIES);
                $table->foreign(AnimeSeries::ATTRIBUTE_SERIES)->references(Series::ATTRIBUTE_ID)->on(Series::TABLE)->cascadeOnDelete();
                $table->primary([AnimeSeries::ATTRIBUTE_ANIME, AnimeSeries::ATTRIBUTE_SERIES]);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(AnimeSeries::TABLE);
    }
};
