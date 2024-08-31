<?php

declare(strict_types=1);

use App\Models\BaseModel;
use App\Models\List\External\ExternalToken;
use App\Models\List\ExternalProfile;
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
        if (! Schema::hasTable(ExternalToken::TABLE)) {
            Schema::create(ExternalToken::TABLE, function (Blueprint $table) {
                $table->id(ExternalToken::ATTRIBUTE_ID);
                $table->longText(ExternalToken::ATTRIBUTE_ACCESS_TOKEN)->nullable();
                $table->longText(ExternalToken::ATTRIBUTE_REFRESH_TOKEN)->nullable();

                $table->unsignedBigInteger(ExternalToken::ATTRIBUTE_PROFILE)->nullable()->unique();
                $table->foreign(ExternalToken::ATTRIBUTE_PROFILE)->references(ExternalProfile::ATTRIBUTE_ID)->on(ExternalProfile::TABLE)->cascadeOnDelete();

                $table->timestamps(6);
                $table->softDeletes(BaseModel::ATTRIBUTE_DELETED_AT, 6);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(ExternalToken::TABLE);
    }
};
