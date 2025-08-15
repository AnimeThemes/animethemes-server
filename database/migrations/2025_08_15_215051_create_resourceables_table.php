<?php

declare(strict_types=1);

use App\Models\Wiki\ExternalResource;
use App\Pivots\Morph\Resourceable;
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
        if (! Schema::hasTable(Resourceable::TABLE)) {
            Schema::create(Resourceable::TABLE, function (Blueprint $table) {
                $table->unsignedBigInteger(Resourceable::ATTRIBUTE_RESOURCE);
                $table->foreign(Resourceable::ATTRIBUTE_RESOURCE)->references(ExternalResource::ATTRIBUTE_ID)->on(ExternalResource::TABLE)->cascadeOnDelete();

                $table->morphs(Resourceable::RELATION_RESOURCEABLE);
                $table->string(Resourceable::ATTRIBUTE_AS)->nullable();

                $table->timestamps(6);

                $table->primary([
                    Resourceable::ATTRIBUTE_RESOURCE,
                    Resourceable::ATTRIBUTE_RESOURCEABLE_TYPE,
                    Resourceable::ATTRIBUTE_RESOURCEABLE_ID,
                ]);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(Resourceable::TABLE);
    }
};
