<?php

declare(strict_types=1);

use App\Models\BaseModel;
use App\Models\List\External\ExternalEntry;
use App\Models\List\ExternalProfile;
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
        if (! Schema::hasTable(ExternalEntry::TABLE)) {
            Schema::create(ExternalEntry::TABLE, function (Blueprint $table) {
                $table->timestamps(6);
                $table->softDeletes(BaseModel::ATTRIBUTE_DELETED_AT, 6);
                $table->id(ExternalEntry::ATTRIBUTE_ID);
                $table->float(ExternalEntry::ATTRIBUTE_SCORE)->nullable();
                $table->integer(ExternalEntry::ATTRIBUTE_WATCH_STATUS)->nullable();
                $table->boolean(ExternalEntry::ATTRIBUTE_IS_FAVOURITE)->default(false);

                $table->unsignedBigInteger(ExternalEntry::ATTRIBUTE_ANIME)->nullable();
                $table->foreign(ExternalEntry::ATTRIBUTE_ANIME)->references(Anime::ATTRIBUTE_ID)->on(Anime::TABLE)->cascadeOnDelete();

                $table->unsignedBigInteger(ExternalEntry::ATTRIBUTE_EXTERNAL_PROFILE)->nullable();
                $table->foreign(ExternalEntry::ATTRIBUTE_EXTERNAL_PROFILE)->references(ExternalProfile::ATTRIBUTE_ID)->on(ExternalProfile::TABLE)->cascadeOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(ExternalEntry::TABLE);
    }
};
