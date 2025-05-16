<?php

declare(strict_types=1);

use App\Models\Aggregate\LikeAggregate;
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
        if (!Schema::hasTable(LikeAggregate::TABLE)) {
            Schema::create(LikeAggregate::TABLE, function (Blueprint $table) {
                $table->morphs(LikeAggregate::ATTRIBUTE_LIKEABLE);
                $table->integer(LikeAggregate::ATTRIBUTE_VALUE)->default(0);
                $table->primary([LikeAggregate::ATTRIBUTE_LIKEABLE_ID, LikeAggregate::ATTRIBUTE_LIKEABLE_TYPE]);
                $table->index([LikeAggregate::ATTRIBUTE_LIKEABLE_ID, LikeAggregate::ATTRIBUTE_LIKEABLE_TYPE]);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(LikeAggregate::TABLE);
    }
};
