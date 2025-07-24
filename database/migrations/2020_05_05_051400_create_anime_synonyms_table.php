<?php

declare(strict_types=1);

use App\Constants\ModelConstants;
use App\Enums\Models\Wiki\AnimeSynonymType;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeSynonym;
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
        if (! Schema::hasTable(AnimeSynonym::TABLE)) {
            Schema::create(AnimeSynonym::TABLE, function (Blueprint $table) {
                $table->id(AnimeSynonym::ATTRIBUTE_ID);
                $table->timestamps(6);
                $table->softDeletes(ModelConstants::ATTRIBUTE_DELETED_AT, 6);
                $table->string(AnimeSynonym::ATTRIBUTE_TEXT)->nullable();
                $table->integer(AnimeSynonym::ATTRIBUTE_TYPE)->default(AnimeSynonymType::OTHER->value);

                $table->unsignedBigInteger(AnimeSynonym::ATTRIBUTE_ANIME);
                $table->foreign(AnimeSynonym::ATTRIBUTE_ANIME)->references(Anime::ATTRIBUTE_ID)->on(Anime::TABLE)->cascadeOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(AnimeSynonym::TABLE);
    }
};
