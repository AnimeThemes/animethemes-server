<?php

declare(strict_types=1);

use App\Models\BaseModel;
use App\Models\List\UserExternalListEntry;
use App\Models\List\UserExternalProfile;
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
        if (! Schema::hasTable(UserExternalListEntry::TABLE)) {
            Schema::create(UserExternalListEntry::TABLE, function (Blueprint $table) {
                $table->timestamps(6);
                $table->softDeletes(BaseModel::ATTRIBUTE_DELETED_AT, 6);
                $table->id(UserExternalListEntry::ATTRIBUTE_ID);
                $table->float(UserExternalListEntry::ATTRIBUTE_SCORE)->nullable();
                $table->integer(UserExternalListEntry::ATTRIBUTE_WATCH_STATUS)->nullable();

                $table->unsignedBigInteger(UserExternalListEntry::ATTRIBUTE_ANIME);
                $table->foreign(UserExternalListEntry::ATTRIBUTE_ANIME)->references(Anime::ATTRIBUTE_ID)->on(Anime::TABLE)->cascadeOnDelete();

                $table->unsignedBigInteger(UserExternalListEntry::ATTRIBUTE_USER_PROFILE);
                $table->foreign(UserExternalListEntry::ATTRIBUTE_USER_PROFILE)->references(UserExternalProfile::ATTRIBUTE_ID)->on(UserExternalProfile::TABLE)->cascadeOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(UserExternalListEntry::TABLE);
    }
};
