<?php

declare(strict_types=1);

use App\Models\Service\ViewAggregate;
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
        if (!Schema::hasTable(ViewAggregate::TABLE)) {
            Schema::create(ViewAggregate::TABLE, function (Blueprint $table) {
                $table->morphs(ViewAggregate::ATTRIBUTE_VIEWABLE);
                $table->integer(ViewAggregate::ATTRIBUTE_VALUE)->default(0);
                $table->primary([ViewAggregate::ATTRIBUTE_VIEWABLE_ID, ViewAggregate::ATTRIBUTE_VIEWABLE_TYPE]);
                $table->index([ViewAggregate::ATTRIBUTE_VIEWABLE_ID, ViewAggregate::ATTRIBUTE_VIEWABLE_TYPE]);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(ViewAggregate::TABLE);
    }
};
