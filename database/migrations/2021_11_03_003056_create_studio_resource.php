<?php

declare(strict_types=1);

use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Studio;
use App\Pivots\Wiki\StudioResource;
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
        if (! Schema::hasTable(StudioResource::TABLE)) {
            Schema::create(StudioResource::TABLE, function (Blueprint $table) {
                $table->timestamps(6);
                $table->unsignedBigInteger(StudioResource::ATTRIBUTE_STUDIO);
                $table->foreign(StudioResource::ATTRIBUTE_STUDIO)->references(Studio::ATTRIBUTE_ID)->on(Studio::TABLE)->cascadeOnDelete();
                $table->unsignedBigInteger(StudioResource::ATTRIBUTE_RESOURCE);
                $table->foreign(StudioResource::ATTRIBUTE_RESOURCE)->references(ExternalResource::ATTRIBUTE_ID)->on(ExternalResource::TABLE)->cascadeOnDelete();
                $table->primary([StudioResource::ATTRIBUTE_STUDIO, StudioResource::ATTRIBUTE_RESOURCE]);
                $table->string(StudioResource::ATTRIBUTE_AS)->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(StudioResource::TABLE);
    }
};
